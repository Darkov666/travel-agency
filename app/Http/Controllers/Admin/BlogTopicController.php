<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogTopic;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class BlogTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Admin/BlogTopics/Index', [
            'topics' => BlogTopic::withCount('posts')->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/BlogTopics/Form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_topics',
            'slug' => 'nullable|string|max:255|unique:blog_topics',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        BlogTopic::create($validated);

        return redirect()->route('admin.blog-topics.index')->with('success', 'Topic created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogTopic $blogTopic)
    {
        return Inertia::render('Admin/BlogTopics/Form', [
            'topic' => $blogTopic
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogTopic $blogTopic)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('blog_topics')->ignore($blogTopic->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_topics')->ignore($blogTopic->id)],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $blogTopic->update($validated);

        return redirect()->route('admin.blog-topics.index')->with('success', 'Topic updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogTopic $blogTopic)
    {
        if ($blogTopic->posts()->count() > 0) {
            return back()->with('error', 'Cannot delete topic with associated posts.');
        }

        $blogTopic->delete();

        return redirect()->route('admin.blog-topics.index')->with('success', 'Topic deleted successfully.');
    }
}
