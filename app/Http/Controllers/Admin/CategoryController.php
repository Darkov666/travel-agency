<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Category::query();

        if ($user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        }
        // If root, maybe show all or scoped? Usually root sees everything or can filter.
        // For simplicity, traits handle scope. If Trait is used, global scope applies.

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $query->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        if (auth()->user()->organization_id) {
            $validated['organization_id'] = auth()->user()->organization_id;
        }

        Category::create($validated);

        return redirect()->back()->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category)
    {
        // Policy check?
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($category->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return redirect()->back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted.');
    }
}
