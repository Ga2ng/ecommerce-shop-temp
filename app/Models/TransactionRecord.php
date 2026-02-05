<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionRecord extends Model
{
    protected $table = 'transaction_records';

    public const EMAIL_STATUS_PENDING = 'pending';
    public const EMAIL_STATUS_SENT = 'sent';

    protected $fillable = [
        'order_id',
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'subtotal',
        'shipping_cost',
        'total',
        'payment_type',
        'payment_method_label',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'paid_at',
        'email_status',
        'email_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
            'email_sent_at' => 'datetime',
        ];
    }

    public function isEmailSent(): bool
    {
        return $this->email_status === self::EMAIL_STATUS_SENT;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label pembayaran untuk tampilan (credit_card → Kartu Kredit, dll).
     */
    public static function paymentTypeLabel(string $type): string
    {
        $labels = [
            'credit_card' => 'Kartu Kredit',
            'bank_transfer' => 'Transfer Bank',
            'echannel' => 'Mandiri Bill',
            'bca_va' => 'BCA VA',
            'bni_va' => 'BNI VA',
            'bri_va' => 'BRI VA',
            'permata_va' => 'Permata VA',
            'cimb_va' => 'CIMB VA',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            'cstore' => 'Convenience Store',
            'akulaku' => 'Akulaku',
            'kredivo' => 'Kredivo',
        ];

        return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }
}
