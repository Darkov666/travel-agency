<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'commentable_id' => 'required|integer',
            'commentable_type' => 'required|string|in:App\Models\BlogPost,App\Models\Review',
            'guest_name' => 'nullable|string|max:255',
            'guest_email' => 'nullable|email|max:255',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = \App\Models\Comment::create([
            'content' => $validated['content'],
            'commentable_id' => $validated['commentable_id'],
            'commentable_type' => $validated['commentable_type'],
            'user_id' => auth()->id(),
            'guest_name' => auth()->check() ? null : $validated['guest_name'],
            'guest_email' => auth()->check() ? null : ($validated['guest_email'] ?? null),
            'parent_id' => $validated['parent_id'] ?? null,
            'ip_address' => $request->ip(),
            'is_approved' => false,
        ]);

        // Notify Admins
        // 1. Global Admins (Hardcoded for now + Root)
        $adminEmails = ['admin@example.com', 'rafael@alyara.mx', 'darkov666@darkov.com', 'root@travel.com'];
        $globalAdmins = \App\Models\User::whereIn('email', $adminEmails)->get();

        // 2. Organization Admins (If comment is on a Review)
        $orgAdmins = collect();
        if ($comment->commentable_type === 'App\Models\Review' && $comment->commentable) {
            // Review -> Reservation -> Organization
            $review = $comment->commentable;
            if ($review->reservation && $review->reservation->organization_id) {
                $orgId = $review->reservation->organization_id;
                // Find admins of this organization
                $orgAdmins = \App\Models\User::where('organization_id', $orgId)
                    ->where('role', 'admin') // Assuming 'admin' role for Org Admins
                    ->get();
                \Illuminate\Support\Facades\Log::info("Found " . $orgAdmins->count() . " Org Admins for Org ID: {$orgId}");
            }
        }

        // Merge and Unique
        $allAdmins = $globalAdmins->merge($orgAdmins)->unique('id');

        \Illuminate\Support\Facades\Log::info('Total Admins to Notify: ' . $allAdmins->count(), $allAdmins->pluck('email')->toArray());

        foreach ($allAdmins as $admin) {
            try {
                $admin->notify(new \App\Notifications\NewComment($comment));
                \Illuminate\Support\Facades\Log::info("Notification sent to {$admin->email}");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to notify {$admin->email}: " . $e->getMessage());
            }
        }

        // Fallback check (only if NO admins found at all)
        if ($allAdmins->isEmpty()) {
            \Illuminate\Support\Facades\Mail::to('admin@example.com')->send(new \App\Mail\NewCommentNotification($comment));
        }

        return back()->with('success', 'Comment submitted for moderation.');
    }

    public function like(\App\Models\Comment $comment)
    {
        $user = auth()->user();
        if (!$user) {
            // Guest logic (IP based) - simplified for now
            $attributes = ['ip_address' => request()->ip(), 'user_id' => null];
        } else {
            $attributes = ['user_id' => $user->id];
        }

        $existingLike = $comment->likes()->where($attributes)->first();

        if ($existingLike) {
            $existingLike->delete();
            $message = 'Like removed.';
        } else {
            // Ensure unique like
            $comment->likes()->create($attributes);
            $message = 'Like added.';
        }

        return back()->with('success', $message);
    }
}
