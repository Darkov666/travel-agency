<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProviderController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Providers/Index', [
            'providers' => Provider::latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Providers/Form', [
            'provider' => null,
            'availableServices' => \App\Models\Service::select('id', 'title', 'type')->get(),
            'availableZones' => \App\Models\Zone::select('id', 'name')->get(),
            'allowedTypes' => $this->getAllowedTypes(),
            'allOrganizations' => !auth()->user()->organization_id ? \App\Models\Organization::select('id', 'name')->get() : [],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'partner_id' => 'nullable|string|unique:providers,partner_id',
            'contact_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'provider_type' => 'required|in:transport,tour,water,baggage,groups_lodging',
            'taxpayer_type' => 'nullable|in:physical,legal',
            'full_address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'tax_compliance' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'priority' => 'nullable|integer|between:1,3',
            'is_active' => 'boolean',
        ]);

        // Auto-generate Partner ID if not provided
        if (empty($validated['partner_id'])) {
            $lastId = Provider::max('id') ?? 0;
            $nextId = $lastId + 1;
            $validated['partner_id'] = 'P-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('providers/logos', env('PUBLIC_FILESYSTEM_DISK', 'public'));
            $validated['logo_path'] = $path;
        }

        if ($request->hasFile('tax_compliance')) {
            $path = $request->file('tax_compliance')->store('providers/tax_docs', env('PUBLIC_FILESYSTEM_DISK', 'public'));
            $validated['tax_compliance_path'] = $path;
        }

        // Root can assign specific organization_id if needed, otherwise it falls to default linkage
        if (auth()->user()->organization_id) {
            $validated['organization_id'] = auth()->user()->organization_id;
        }

        $provider = Provider::create($validated);

        // Handle Assignments (Root Only Update)
        if (!auth()->user()->organization_id && $request->has('assigned_organizations')) {
            $provider->assignedOrganizations()->sync($request->input('assigned_organizations'));
        }

        return redirect()->route('admin.providers.edit', $provider)->with('success', 'Provider created successfully.');
    }

    public function edit(Provider $provider)
    {
        return Inertia::render('Admin/Providers/Form', [
            'provider' => $provider ? $provider->load(['vehicles', 'providerServices.service', 'providerServices.zone', 'organization', 'assignedOrganizations']) : null,
            'availableServices' => \App\Models\Service::select('id', 'title', 'type')->get(),
            'availableZones' => \App\Models\Zone::select('id', 'name')->get(),
            'allowedTypes' => $this->getAllowedTypes(),
            'allOrganizations' => !auth()->user()->organization_id ? \App\Models\Organization::select('id', 'name')->get() : [],
        ]);
    }

    private function getAllowedTypes()
    {
        $tenant = auth()->user()->organization;
        if (!$tenant) {
            return ['transport', 'tour', 'water', 'baggage', 'groups_lodging'];
        }

        $modules = $tenant->settings['modules'] ?? ['transport', 'tours', 'shop'];
        $types = [];

        if (in_array('transport', $modules)) {
            $types[] = 'transport';
        }
        if (in_array('tours', $modules)) {
            $types = array_merge($types, ['tour', 'water']);
        }
        if (in_array('baggage', $modules)) {
            $types[] = 'baggage';
        }
        if (in_array('groups_lodging', $modules)) {
            $types[] = 'groups_lodging';
        }

        return array_unique($types);
    }

    public function update(Request $request, Provider $provider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'provider_type' => 'required|in:transport,tour,water,baggage,groups_lodging',
            'taxpayer_type' => 'nullable|in:physical,legal',
            'full_address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'tax_compliance' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'priority' => 'nullable|integer|between:1,3',
            'is_active' => 'boolean',
            'exchange_rate' => 'nullable|numeric|min:0',
        ]);

        // Partner ID is usually immutable or handled separately, removing from validation to avoid issues if not passed or passed same.
        // If we want to allow editing, we need unique rule ignoring current id.
        // Assuming auto-generated ones shouldn't be touched often, but let's allow updating if explicitly needed?
        // User asked for "autoincremental and unique". Usually implies read-only. 
        // I won't update partner_id here to protect it.

        if ($request->has('exchange_rate') && $provider->organization) {
            $provider->organization->update(['exchange_rate' => $validated['exchange_rate']]);
        }

        if ($request->hasFile('logo')) {
            if ($provider->logo_path) {
                Storage::disk(env('PUBLIC_FILESYSTEM_DISK', 'public'))->delete($provider->logo_path);
            }
            $path = $request->file('logo')->store('providers/logos', env('PUBLIC_FILESYSTEM_DISK', 'public'));
            $validated['logo_path'] = $path;
        }

        if ($request->hasFile('tax_compliance')) {
            if ($provider->tax_compliance_path) {
                Storage::disk(env('PUBLIC_FILESYSTEM_DISK', 'public'))->delete($provider->tax_compliance_path);
            }
            $path = $request->file('tax_compliance')->store('providers/tax_docs', env('PUBLIC_FILESYSTEM_DISK', 'public'));
            $validated['tax_compliance_path'] = $path;
        }

        $provider->update($validated);

        // Handle Assignments (Root Only Update)
        if (!auth()->user()->organization_id && $request->has('assigned_organizations')) {
            $provider->assignedOrganizations()->sync($request->input('assigned_organizations'));
        }

        return back()->with('success', 'Provider updated successfully.');
    }

    public function destroy(Provider $provider)
    {
        $provider->delete();
        return redirect()->route('admin.providers.index')->with('success', 'Provider deleted.');
    }
}
