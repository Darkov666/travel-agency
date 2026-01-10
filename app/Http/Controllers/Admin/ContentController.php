<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ContentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->isPlatformAdmin() && !$user->organization_id) {
            abort(403, 'Content management is restricted.');
        }

        $orgId = $user->organization_id; // Null for Root (Global Site)

        return Inertia::render('Admin/Content/Index', [
            'blocks' => ContentBlock::where('organization_id', $orgId)->get()->groupBy('group'),
        ]);
    }

    public function update(Request $request)
    {
        $blocks = $request->input('blocks', []);
        $orgId = Auth::user()->organization_id;

        foreach ($blocks as $index => $block) {
            $value = $block['value'];

            // Handle file upload
            if ($request->hasFile("blocks.{$index}.file")) {
                $file = $request->file("blocks.{$index}.file");
                $path = $file->store('content', 'public');
                $value = '/storage/' . $path;
            }

            // Generate key if missing (for custom blocks)
            $key = $block['key'];
            if (empty($key)) {
                $base = \Illuminate\Support\Str::slug($block['label'] ?? 'block');
                $key = $base . '_' . uniqid();
            }

            ContentBlock::updateOrCreate(
                [
                    'key' => $key,
                    'organization_id' => $orgId
                ],
                [
                    'value' => $value,
                    'type' => $block['type'] ?? 'text',
                    'group' => $block['group'] ?? 'general',
                ]
            );
        }

        // Clear cache scoped by Org?
        Cache::forget('content_blocks_' . ($orgId ?? 'global'));

        return redirect()->back()->with('success', 'Content updated successfully.');
    }
}
