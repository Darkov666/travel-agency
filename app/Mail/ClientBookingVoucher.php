<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientBookingVoucher extends Mailable
{
    use Queueable, SerializesModels;

    public $item;
    protected $pdfService;

    /**
     * Create a new message instance.
     */
    public function __construct($item)
    {
        $this->item = $item;
        $this->pdfService = new \App\Services\PdfGeneratorService();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Voucher - #' . $this->item->reservation->booking_ref,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.client_booking_voucher',
            with: ['item' => $this->item]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $pdfContent = $this->pdfService->generateClientVoucher($this->item);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn() => $pdfContent,
                'Voucher-' . $this->item->reservation->booking_ref . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
