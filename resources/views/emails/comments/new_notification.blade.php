<!DOCTYPE html>
<html>

<head>
    <title>New Comment Alert</title>
</head>

<body style="font-family: Arial, sans-serif;">
    <h2>New Comment Submitted</h2>
    <p>A new comment has been posted and requires your approval.</p>

    <div style="border: 1px solid #ccc; padding: 15px; background: #f9f9f9; margin-bottom: 20px;">
        <p><strong>Author:</strong> {{ $comment->user ? $comment->user->name : $comment->guest_name . ' (Guest)' }}</p>
        <p><strong>Email:</strong> {{ $comment->user ? $comment->user->email : $comment->guest_email }}</p>
        <p><strong>On:</strong> {{ class_basename($comment->commentable_type) }} #{{ $comment->commentable_id }}</p>
        <p><strong>Content:</strong></p>
        <blockquote style="font-style: italic;">
            {{ $comment->content }}
        </blockquote>
    </div>

    <p style="text-align: center; margin-top: 30px;">
        <a href="{{ $approveUrl }}"
            style="background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px; font-weight: bold;">
            Approve
        </a>
        <a href="{{ $rejectUrl }}"
            style="background-color: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px; font-weight: bold;">
            Reject (Hide)
        </a>
        <a href="{{ $deleteUrl }}"
            style="background-color: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Delete
        </a>
    </p>

    <p style="text-align: center; margin-top: 20px;">
        <a href="{{ route('admin.comments.index') }}" style="color: #666; text-decoration: underline; font-size: 14px;">
            Go to Moderation Panel
        </a>
    </p>
</body>

</html>