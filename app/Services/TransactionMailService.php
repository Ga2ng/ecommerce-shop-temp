<?php

namespace App\Services;

use App\Mail\TransactionReceiptMail;
use App\Models\TransactionRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TransactionMailService
{
    /**
     * Kirim email struk ke customer. Jika berhasil, update email_status = sent dan email_sent_at.
     *
     * @return bool true jika terkirim, false jika gagal
     */
    public function sendReceipt(TransactionRecord $record): bool
    {
        $record->refresh();

        if (empty($record->customer_email)) {
            Log::warning('TransactionReceipt: customer_email kosong', ['transaction_id' => $record->id]);
            return false;
        }

        try {
            $record->load('order.items');
            Mail::to($record->customer_email)->send(new TransactionReceiptMail($record));

            $record->email_status = TransactionRecord::EMAIL_STATUS_SENT;
            $record->email_sent_at = now();
            $record->save();

            Log::info('Transaction receipt email sent', [
                'transaction_id' => $record->id,
                'order_number' => $record->order_number,
                'to' => $record->customer_email,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Transaction receipt email failed: ' . $e->getMessage(), [
                'transaction_id' => $record->id,
                'order_number' => $record->order_number,
            ]);
            return false;
        }
    }
}
