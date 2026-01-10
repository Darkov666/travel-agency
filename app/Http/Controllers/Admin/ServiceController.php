<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::whereNotIn('type', ['merchandise', 'package'])
            ->orderBy('created_at', 'desc');

        if ($request->has('provider_id') && $request->provider_id !== 'all') {
            $query->where('provider_id', $request->provider_id);
        }

        return Inertia::render('Admin/Services/Index', [
            'services' => $query->paginate(10)->withQueryString(),
            'providers' => \App\Models\Provider::select('id', 'name')->get(),
            'filters' => $request->only(['provider_id']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Services/Create', [
            'providers' => \App\Models\Provider::select('id', 'name')->get(),
            'categories' => \App\Models\Category::where('is_active', true)->get(),
            'allowedTypes' => $this->getAllowedTypes(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'net_price' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'commission_type' => 'required|in:fixed,percentage',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'provider_id' => 'nullable|exists:providers,id',
            'category_id' => 'nullable|exists:categories,id',
            'new_category_name' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'gallery' => 'nullable|array',
        ]);

        // Handle new category creation
        if (!empty($request->new_category_name)) {
            $newCategory = \App\Models\Category::firstOrCreate(
                ['name' => $request->new_category_name],
                [
                    'slug' => Str::slug($request->new_category_name),
                    'organization_id' => auth()->user()->organization_id ?? 1,
                    'is_active' => true,
                    'type' => 'service'
                ]
            );
            $validated['category_id'] = $newCategory->id;
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['currency'] = 'MXN';
        $validated['organization_id'] = auth()->user()->organization_id ?? 1;

        Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return Inertia::render('Admin/Services/Edit', [
            'service' => $service,
            'providers' => \App\Models\Provider::select('id', 'name')->get(),
            'categories' => \App\Models\Category::where('is_active', true)->get(),
            'allowedTypes' => $this->getAllowedTypes(),
        ]);
    }

    private function getAllowedTypes()
    {
        $tenant = auth()->user()->organization;
        if (!$tenant) {
            return ['transfer', 'hourly', 'tour', 'package', 'water', 'attraction', 'special', 'merchandise'];
        }

        $modules = $tenant->settings['modules'] ?? ['transport', 'tours', 'shop'];
        $types = [];

        if (in_array('transport', $modules)) {
            $types = array_merge($types, ['transfer', 'hourly']);
        }
        if (in_array('tours', $modules)) {
            $types = array_merge($types, ['tour', 'package', 'water', 'attraction']);
        }
        if (in_array('shop', $modules)) {
            $types = array_merge($types, ['merchandise', 'special']);
        }
        // Fallback if empty but tenant exists (shouldn't happen)
        if (empty($types)) {
            $types = ['transfer'];
        }

        return array_unique($types);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'net_price' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'commission_type' => 'required|in:fixed,percentage',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'provider_id' => 'nullable|exists:providers,id',
            'category_id' => 'nullable|exists:categories,id',
            'new_category_name' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'gallery' => 'nullable|array',
        ]);

        // Handle new category creation
        if (!empty($request->new_category_name)) {
            $newCategory = \App\Models\Category::firstOrCreate(
                ['name' => $request->new_category_name],
                [
                    'slug' => Str::slug($request->new_category_name),
                    'organization_id' => auth()->user()->organization_id ?? 1,
                    'is_active' => true,
                    'type' => 'service'
                ]
            );
            $validated['category_id'] = $newCategory->id;
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}
