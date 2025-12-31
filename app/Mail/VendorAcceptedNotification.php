<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VendorAcceptedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $item;

    /**
     * Create a new message instance.
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Provider Accepted Service - #' . $this->item->reservation->booking_ref,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Route for Admin to Cancel this assignment
        // We need a route for this. Let's assume 'admin.reservations.cancel_vendor'
        return new Content(
            view: 'emails.vendor_accepted_notification',
            with: [
                'item' => $this->item,
                'cancelUrl' => route('admin.reservations.cancel_vendor', $this->item->id)
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
        return [];
    }
}
