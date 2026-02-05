<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionRecord;
use App\Services\TransactionMailService;
use Illuminate\Http\RedirectResponse;

class TransactionReceiptController extends Controller
{
    public function __construct(
        protected TransactionMailService $transactionMailService
    ) {}

    /**
     * Kirim ulang / kirim manual email struk ke customer (dari admin).
     */
    public function send(TransactionRecord $transaction): RedirectResponse
    {
        $sent = $this->transactionMailService->sendReceipt($transaction);

        if ($sent) {
            return redirect()
                ->route('admin.transactions.index')
                ->with('success', 'Email struk berhasil dikirim ke ' . $transaction->customer_email);
        }

        return redirect()
            ->route('admin.transactions.index')
            ->with('error', 'Gagal mengirim email struk. Cek log atau konfigurasi MAIL_* di .env.');
    }
}
