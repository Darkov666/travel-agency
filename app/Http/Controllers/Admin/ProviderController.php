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
            'provider_type' => 'required|in:transport,tour,water',
            'taxpayer_type' => 'nullable|in:physical,legal',
            'full_address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'tax_compliance' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'priority' => 'nullable|integer|between:1,3',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('providers/logos', env('PUBLIC_FILESYSTEM_DISK', 'public'));
            $validated['logo_path'] = $path;
        }

        if ($request->hasFile('tax_compliance')) {
            $path = $request->file('tax_compliance')->store('providers/tax_docs', env('PUBLIC_FILESYSTEM_DISK', 'public'));
            $validated['tax_compliance_path'] = $path;
        }

        $provider = Provider::create($validated);

        return redirect()->route('admin.providers.edit', $provider)->with('success', 'Provider created successfully.');
    }

    public function edit(Provider $provider)
    {
        return Inertia::render('Admin/Providers/Form', [
            'provider' => $provider->load(['vehicles', 'providerServices.service', 'providerServices.zone', 'organization']),
            'availableServices' => \App\Models\Service::select('id', 'title', 'type')->get(), // Global services
            'availableZones' => \App\Models\Zone::select('id', 'name')->get(), // For linking prices
        ]);
    }

    public function update(Request $request, Provider $provider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'partner_id' => 'nullable|string|unique:providers,partner_id,' . $provider->id,
            'contact_name' => 'nullable|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'provider_type' => 'required|in:transport,tour,water',
            'taxpayer_type' => 'nullable|in:physical,legal',
            'full_address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'tax_compliance' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'priority' => 'nullable|integer|between:1,3',
            'is_active' => 'boolean',
            'exchange_rate' => 'nullable|numeric|min:0',
        ]);

        if ($request->has('exchange_rate') && $provider->organization) {
            $provider->organization->update(['exchange_rate' => $validated['exchange_rate']]);
        }

        if ($request->hasFile('logo')) {
            // Delete old
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

        return back()->with('success', 'Provider updated successfully.');
    }

    public function destroy(Provider $provider)
    {
        $provider->delete();
        return redirect()->route('admin.providers.index')->with('success', 'Provider deleted.');
    }
}
