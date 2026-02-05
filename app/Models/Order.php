<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_province',
        'shipping_district',
        'shipping_postal_code',
        'rajaongkir_city_id',
        'shipping_courier',
        'shipping_service',
        'shipping_cost',
        'subtotal',
        'total',
        'payment_method',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'payment_status',
        'paid_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8)) . '-' . now()->format('Ymd');
            }
        });
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment logs for the order.
     */
    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    /**
     * Get the transaction record for dashboard (one per paid order).
     */
    public function transactionRecord(): HasOne
    {
        return $this->hasOne(TransactionRecord::class);
    }

    /**
     * Check if order is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['cart', 'pending_payment', 'expired']);
    }

    /**
     * Scope a query to only include pending payment orders.
     */
    public function scopePendingPayment($query)
    {
        return $query->where('status', 'pending_payment');
    }

    /**
     * Scope a query to only include expired orders.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('status', 'pending_payment')
                  ->where('expires_at', '<', now());
            });
    }

    /**
     * Tandai order sebagai lunas dari payload Midtrans (settlement/capture).
     * Mengurangi stok, membuat TransactionRecord. Idempotent: aman dipanggil 2x.
     * Dipanggil dari WebhookController dan dari PaymentController::success (fallback jika webhook tidak jalan).
     *
     * @return bool true jika status di-update, false jika sudah paid
     */
    public function markAsPaidFromPayload(array $payload): bool
    {
        $this->refresh();

        if ($this->status === 'paid') {
            return false;
        }

        DB::transaction(function () use ($payload) {
            $this->status = 'paid';
            $this->payment_status = $payload['transaction_status'] ?? 'settlement';
            $this->midtrans_transaction_id = $payload['transaction_id'] ?? null;
            $this->midtrans_payment_type = $payload['payment_type'] ?? null;
            $this->paid_at = now();
            $this->save();

            foreach ($this->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $product->confirmReservedStock($item->quantity);
                }
            }

            if (!TransactionRecord::where('order_id', $this->id)->exists()) {
                $paymentType = $payload['payment_type'] ?? null;
                TransactionRecord::create([
                    'order_id' => $this->id,
                    'order_number' => $this->order_number,
                    'user_id' => $this->user_id,
                    'customer_name' => $this->customer_name,
                    'customer_email' => $this->customer_email,
                    'customer_phone' => $this->customer_phone,
                    'subtotal' => $this->subtotal,
                    'shipping_cost' => $this->shipping_cost ?? 0,
                    'total' => $this->total,
                    'payment_type' => $paymentType,
                    'payment_method_label' => $paymentType ? TransactionRecord::paymentTypeLabel($paymentType) : null,
                    'midtrans_order_id' => $this->midtrans_order_id,
                    'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
                    'paid_at' => $this->paid_at,
                ]);
            }
        });

        return true;
    }

    /**
     * Buat TransactionRecord jika belum ada (order sudah paid). Untuk perbaikan / backfill.
     */
    public function ensureTransactionRecord(): void
    {
        if (TransactionRecord::where('order_id', $this->id)->exists()) {
            return;
        }

        $paymentType = $this->midtrans_payment_type ?? null;
        TransactionRecord::create([
            'order_id' => $this->id,
            'order_number' => $this->order_number,
            'user_id' => $this->user_id,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'subtotal' => $this->subtotal,
            'shipping_cost' => $this->shipping_cost ?? 0,
            'total' => $this->total,
            'payment_type' => $paymentType,
            'payment_method_label' => $paymentType ? TransactionRecord::paymentTypeLabel($paymentType) : null,
            'midtrans_order_id' => $this->midtrans_order_id,
            'midtrans_transaction_id' => $this->midtrans_transaction_id,
            'paid_at' => $this->paid_at,
        ]);
    }
}
