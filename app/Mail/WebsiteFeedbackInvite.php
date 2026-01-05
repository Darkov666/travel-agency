<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebsiteFeedbackInvite extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $feedback;

    public function __construct($feedback)
    {
        $this->feedback = $feedback;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We value your feedback on our website!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback.website',
            with: ['url' => route('feedback.website.show', $this->feedback->token)],
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
