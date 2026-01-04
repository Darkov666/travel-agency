<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class NewComment extends Notification
{
    use Queueable;

    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $approveUrl = URL::signedRoute('comments.email.approve', ['comment' => $this->comment->id]);
        $rejectUrl = URL::signedRoute('comments.email.reject', ['comment' => $this->comment->id]);
        $deleteUrl = URL::signedRoute('comments.email.delete', ['comment' => $this->comment->id]);

        $author = $this->comment->user ? $this->comment->user->name : ($this->comment->guest_name . ' (Guest)');
        $email = $this->comment->user ? $this->comment->user->email : $this->comment->guest_email;
        $context = class_basename($this->comment->commentable_type) . ' #' . $this->comment->commentable_id;

        return (new MailMessage)
            ->subject('New Comment Alert')
            ->view('emails.comments.new_notification', [
                'comment' => $this->comment,
                'approveUrl' => $approveUrl,
                'rejectUrl' => $rejectUrl,
                'deleteUrl' => $deleteUrl,
            ]);
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
