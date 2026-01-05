<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ZoneController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Zones', [
            'zones' => Zone::with('provider')->get(),
            'providers' => \App\Models\Provider::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:zones',
            'priority' => 'required|integer',
            'transfer_time_minutes' => 'required|integer|min:0',
            'color' => 'required|string|max:7',
            'coordinates' => 'nullable', // Allow string or JSON
            'provider_id' => 'nullable|exists:providers,id',
            'service_type' => 'nullable|string|in:transfer,tour,all',
        ]);

        // If coordinates is an array, Laravel's array cast will handle serialization automatically.
        // If it's a JSON string, we should decode it to array first to ensure consistency, 
        // or just pass it if Laravel handles it.
        // Best practice with 'array' cast: pass an Array.
        if (isset($validated['coordinates']) && is_string($validated['coordinates'])) {
            $decoded = json_decode($validated['coordinates'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['coordinates'] = $decoded;
            }
        }
        // If it was already an array (from Vue as JSON), it stays array.

        Zone::create($validated);

        return redirect()->back()->with('success', 'Zone created successfully.');
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:zones,name,' . $zone->id,
            'priority' => 'required|integer',
            'transfer_time_minutes' => 'required|integer|min:0',
            'color' => 'required|string|max:7',
            'coordinates' => 'nullable',
            'provider_id' => 'nullable|exists:providers,id',
            'service_type' => 'nullable|string|in:transfer,tour,all',
        ]);

        if (isset($validated['coordinates']) && is_string($validated['coordinates'])) {
            $decoded = json_decode($validated['coordinates'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['coordinates'] = $decoded;
            }
        }

        $zone->update($validated);

        return redirect()->back()->with('success', 'Zone updated successfully.');
    }

    public function destroy(Zone $zone)
    {
        // Manually delete related provider services to avoid FK constraint violation
        // (in case DB cascade isn't working or missing)
        $zone->providerServices()->delete();

        $zone->delete();
        return redirect()->back()->with('success', 'Zone deleted successfully.');
    }
}
