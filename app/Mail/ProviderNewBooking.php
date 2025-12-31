<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ReservationItem;

class ProviderNewBooking extends Mailable
{
    use Queueable, SerializesModels;

    public $item;
    public $token;
    protected $pdfService;

    /**
     * Create a new message instance.
     */
    public function __construct(ReservationItem $item, $token)
    {
        $this->item = $item;
        $this->token = $token;
        $this->pdfService = new \App\Services\PdfGeneratorService();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Service Request - Booking #' . $this->item->reservation->booking_ref,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.provider_new_booking',
            with: [
                'item' => $this->item,
                'url' => route('vendor.confirm', ['token' => $this->token]),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $pdfContent = $this->pdfService->generateProviderWorkOrder($this->item);

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn() => $pdfContent,
                'WorkOrder-' . $this->item->reservation->booking_ref . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
