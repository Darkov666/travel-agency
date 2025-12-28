<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogTopic;
use App\Models\Comment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['topic', 'author'])
            ->where('is_published', true);

        // Filter by Topic
        if ($request->has('topic') && $request->topic !== 'all') {
            $query->whereHas('topic', function ($q) use ($request) {
                $q->where('slug', $request->topic);
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Sort
        $query->latest('published_at');

        $posts = $query->paginate(9)->withQueryString();

        $topics = BlogTopic::all();

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'topics' => $topics,
            'filters' => $request->only(['search', 'topic']),
        ]);
    }

    public function show($slug)
    {
        $post = BlogPost::with(['topic', 'author', 'comments.user'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $post->loadCount(['likes', 'comments']);

        $isLiked = false;
        $isSaved = false;

        if (Auth::check()) {
            $isLiked = $post->likes()->where('user_id', Auth::id())->exists();
            $isSaved = $post->savedBy()->where('user_id', Auth::id())->exists();
        } else {
            $isLiked = $post->likes()->where('ip_address', request()->ip())->whereNull('user_id')->exists();
            $isSaved = false; // Guests cannot save posts
        }

        // Transform comments to include isLiked status for the current user
        $post->comments->transform(function ($comment) {
            if (Auth::check()) {
                $comment->isLiked = $comment->likes()->where('user_id', Auth::id())->exists();
            } else {
                $comment->isLiked = $comment->likes()->where('ip_address', request()->ip())->whereNull('user_id')->exists();
            }
            $comment->likes_count = $comment->likes()->count();
            return $comment;
        });

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'isLiked' => $isLiked,
            'isSaved' => $isSaved,
        ]);
    }

    public function comment(Request $request, $slug)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post = BlogPost::where('slug', $slug)->firstOrFail();

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->user_id = Auth::id();
        $comment->blog_post_id = $post->id;
        $comment->ip_address = $request->ip();
        // Auto-approve if authenticated, otherwise require moderation
        $comment->is_approved = Auth::check();
        $comment->save();

        $message = Auth::check() ? 'Comentario publicado correctamente.' : 'Comentario enviado para moderación.';

        return back()->with('success', $message);
    }

    public function like($slug)
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();
        $user = Auth::user();
        $ip = request()->ip();

        $query = $post->likes();

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('ip_address', $ip)->whereNull('user_id');
        }

        if ($query->exists()) {
            $query->delete();
        } else {
            $post->likes()->create([
                'user_id' => $user ? $user->id : null,
                'ip_address' => $ip,
            ]);
        }

        return back();
    }

    public function save($slug)
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        $user->savedPosts()->toggle($post->id);

        return back();
    }

    public function likeComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $user = Auth::user();
        $ip = request()->ip();

        $query = $comment->likes();

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('ip_address', $ip)->whereNull('user_id');
        }

        if ($query->exists()) {
            $query->delete();
        } else {
            $comment->likes()->create([
                'user_id' => $user ? $user->id : null,
                'ip_address' => $ip,
            ]);
        }

        return back();
    }
}
