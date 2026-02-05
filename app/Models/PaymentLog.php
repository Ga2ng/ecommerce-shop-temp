<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Log setiap notifikasi pembayaran dari Midtrans (webhook).
 *
 * Kegunaan:
 * - Audit trail: siapa mengirim apa dan kapan (request_payload, transaction_status, source).
 * - Idempotensi webhook: mencegah proses duplikat (cek existing log sebelum proses).
 * - Debugging: bila order tidak berubah status, cek payment_logs untuk lihat apakah webhook sampai dan valid.
 */
class PaymentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'transaction_status',
        'payment_type',
        'gross_amount',
        'request_payload',
        'response_payload',
        'signature_key',
        'is_valid',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'is_valid' => 'boolean',
            'request_payload' => 'array',
            'response_payload' => 'array',
        ];
    }

    /**
     * Get the order that owns the payment log.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
