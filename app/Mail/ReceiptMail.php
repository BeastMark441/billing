<?php

namespace App\Mail;

use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Receipt $receipt, public string $pdfBytes) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Чек '.$this->receipt->receipt_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.receipt',
            with: [
                'receipt' => $this->receipt,
                'publicUrl' => route('receipts.public.show', ['receipt' => $this->receipt->id, 'token' => $this->receipt->public_token]),
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfBytes, $this->receipt->receipt_number.'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
