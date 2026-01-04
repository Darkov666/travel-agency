<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogTopic;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Blog/Index', [
            'posts' => BlogPost::with(['author', 'topic'])->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Blog/Form', [
            'topics' => BlogTopic::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'topic_id' => 'required|exists:blog_topics,id',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'image_file' => 'nullable|image|max:2048', // Separate input for file
            'is_published' => 'boolean',
        ]);

        $slug = Str::slug($validated['title']);

        // Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('blog', 'public');
        }

        BlogPost::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'topic_id' => $validated['topic_id'],
            'user_id' => Auth::id(),
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'image' => $imagePath ? '/storage/' . $imagePath : null,
            'is_published' => $validated['is_published'],
            'published_at' => $validated['is_published'] ? now() : null,
            'read_time' => ceil(str_word_count(strip_tags($validated['content'])) / 200) . ' min',
        ]);

        return redirect()->route('admin.blog.index')->with('success', 'Post created successfully.');
    }

    public function edit(BlogPost $post)
    {
        return Inertia::render('Admin/Blog/Form', [
            'post' => $post,
            'topics' => BlogTopic::all(),
        ]);
    }

    public function update(Request $request, BlogPost $post)
    {
        // dd($request->all());
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'topic_id' => 'required|exists:blog_topics,id',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'image_file' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
        ]);

        $slug = Str::slug($validated['title']);

        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('blog', 'public');
            $post->image = '/storage/' . $imagePath;
        }

        $post->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'topic_id' => $validated['topic_id'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            // Image handled above
            'is_published' => $validated['is_published'],
            'published_at' => $validated['is_published'] && !$post->is_published ? now() : $post->published_at,
            'read_time' => ceil(str_word_count(strip_tags($validated['content'])) / 200) . ' min',
        ]);

        return redirect()->route('admin.blog.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();
        return redirect()->back()->with('success', 'Post deleted.');
    }
}
