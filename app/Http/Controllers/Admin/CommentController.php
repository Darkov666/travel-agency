<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with(['user', 'commentable']);

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Search by guest name or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $comments = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/Comments/Index', [
            'comments' => $comments,
            'filters' => $request->only(['status', 'search'])
        ]);
    }

    public function show(Comment $comment)
    {
        // Mark notification as read
        // Mark notification as read
        auth()->user()->unreadNotifications()
            ->where('data->comment_id', $comment->id)
            ->get()
            ->markAsRead();

        $comment->load(['user', 'commentable']);
        return Inertia::render('Admin/Comments/Show', [
            'comment' => $comment
        ]);
    }

    public function approve(Comment $comment)
    {
        $comment->update(['is_approved' => true]);
        return back()->with('success', 'Comment approved.');
    }

    public function reject(Comment $comment)
    {
        $comment->update(['is_approved' => false]);
        return back()->with('success', 'Comment rejected/unpublished.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment permanently deleted.');
    }

    public function emailApprove(Comment $comment)
    {
        $comment->update(['is_approved' => true]);
        return redirect()->route('admin.comments.index')->with('success', 'Comment approved via email.');
    }

    public function emailReject(Comment $comment)
    {
        $comment->update(['is_approved' => false]);
        return redirect()->route('admin.comments.index')->with('success', 'Comment rejected (unpublished) via email.');
    }

    public function emailDelete(Comment $comment)
    {
        $comment->delete();
        return redirect()->route('admin.comments.index')->with('success', 'Comment permanently deleted via email.');
    }
}
