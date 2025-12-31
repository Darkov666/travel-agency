<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderService;
use Illuminate\Http\Request;

class ProviderServiceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'zone_id' => 'nullable|exists:zones,id',
            'service_id' => 'nullable|exists:services,id',
            'name' => 'nullable|string|required_without:service_id',
            'description' => 'nullable|string',
            'cost_net' => 'required|numeric|min:0',
            'price_public' => 'required|numeric|min:0',
            'max_pax' => 'nullable|integer',
            'category' => 'nullable|in:standard,vip',
        ]);

        ProviderService::create($validated);

        return back()->with('success', 'Service assigned.');
    }

    public function update(Request $request, ProviderService $providerService)
    {
        $validated = $request->validate([
            'zone_id' => 'nullable|exists:zones,id',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'cost_net' => 'required|numeric|min:0',
            'price_public' => 'required|numeric|min:0',
            'max_pax' => 'nullable|integer',
            'category' => 'nullable|in:standard,vip',
        ]);

        $providerService->update($validated);

        return back()->with('success', 'Service updated.');
    }

    public function destroy(ProviderService $providerService)
    {
        $providerService->delete();
        return back()->with('success', 'Service removed.');
    }
}
