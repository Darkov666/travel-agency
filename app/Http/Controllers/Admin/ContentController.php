<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

class ContentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Content/Index', [
            'blocks' => ContentBlock::all()->groupBy('group'),
        ]);
    }

    public function update(Request $request)
    {
        $blocks = $request->input('blocks', []);

        foreach ($blocks as $index => $block) {
            $value = $block['value'];

            // Handle file upload
            if ($request->hasFile("blocks.{$index}.file")) {
                $file = $request->file("blocks.{$index}.file");
                $path = $file->store('content', 'public');
                $value = '/storage/' . $path;
            }

            ContentBlock::updateOrCreate(
                ['key' => $block['key']],
                [
                    'value' => $value,
                    'type' => $block['type'] ?? 'text',
                    'group' => $block['group'] ?? 'general',
                ]
            );
        }

        // Clear cache if you implement caching for content
        Cache::forget('content_blocks');

        return redirect()->back()->with('success', 'Content updated successfully.');
    }
}
