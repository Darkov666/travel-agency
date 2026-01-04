<?php

namespace App\Mail;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCommentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Comment Waiting for Approval',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.comments.new_notification',
            with: [
                'approveUrl' => \Illuminate\Support\Facades\URL::signedRoute('comments.email.approve', ['comment' => $this->comment->id]),
                'rejectUrl' => \Illuminate\Support\Facades\URL::signedRoute('comments.email.reject', ['comment' => $this->comment->id]),
                'deleteUrl' => \Illuminate\Support\Facades\URL::signedRoute('comments.email.delete', ['comment' => $this->comment->id]),
            ],
        );
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_comment',
            'message' => 'New comment from ' . ($this->comment->user ? $this->comment->user->name : ($this->comment->guest_name ?? 'Guest')),
            'action_url' => route('admin.comments.show', $this->comment->id),
            'comment_id' => $this->comment->id,
        ];
    }
}
