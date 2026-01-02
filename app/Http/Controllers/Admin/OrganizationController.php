<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Organization::withCount('users');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('commercial_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'suspended') {
                $query->where('subscription_status', 'suspended');
            }
        }

        $organizations = $query->latest()->paginate(10)->withQueryString();

        return \Inertia\Inertia::render('Admin/Organizations/Index', [
            'organizations' => $organizations,
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:organizations,slug',
            'commercial_name' => 'required|string|max:255',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        \App\Models\Organization::create([
            'name' => $validated['name'],
            'commercial_name' => $validated['commercial_name'],
            'slug' => $validated['slug'],
            'is_active' => true,
            'settings' => [],
            'subscription_status' => 'active' // Manual creation implies active usually
        ]);

        return redirect()->back()->with('success', 'Organization created successfully.');
    }

    public function update(Request $request, \App\Models\Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'commercial_name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'subscription_status' => 'nullable|string'
        ]);

        $organization->update($validated);

        return redirect()->back()->with('success', 'Organization updated successfully.');
    }

    public function destroy(\App\Models\Organization $organization)
    {
        // Check if has critical data?
        // For now, soft delete or force delete based on req.
        // Assuming force delete is NOT desired if it has reservations.
        // Let's just delete for now (or fail if FK constraints).
        try {
            $organization->delete();
            return redirect()->back()->with('success', 'Organization deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Cannot delete organization with associated data.');
        }
    }
}
