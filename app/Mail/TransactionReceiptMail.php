<?php

namespace App\Mail;

use App\Models\TransactionRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TransactionRecord $transaction
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Struk Pembayaran – ' . $this->transaction->order_number . ' – ' . config('app.name'),
            from: config('mail.from.address'),
            replyTo: [config('mail.from.address')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transaction-receipt'
        );
    }
}
