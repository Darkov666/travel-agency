<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ZoneController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->provider_id) {
            abort(403, 'User is not linked to a provider.');
        }

        return Inertia::render('Provider/Zones/Index', [
            'zones' => Zone::where('provider_id', $user->provider_id)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->provider_id) {
            abort(403, 'User is not linked to a provider.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255', // Uniqueness check should be scoped or loose for now? Let's keep it simple.
            'priority' => 'required|integer',
            'transfer_time_minutes' => 'required|integer|min:0',
            'color' => 'required|string|max:7',
            'coordinates' => 'nullable', // Allow string or JSON
            'service_type' => 'nullable|string|in:transfer,tour,all',
        ]);

        // Force provider_id
        $validated['provider_id'] = $user->provider_id;

        // Coordinates handling
        if (isset($validated['coordinates']) && is_string($validated['coordinates'])) {
            $decoded = json_decode($validated['coordinates'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['coordinates'] = $decoded;
            }
        }

        Zone::create($validated);

        return redirect()->back()->with('success', 'Zone created successfully.');
    }

    public function update(Request $request, Zone $zone)
    {
        $user = Auth::user();

        // Security check: Ensure user owns the zone
        if ($zone->provider_id !== $user->provider_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'required|integer',
            'transfer_time_minutes' => 'required|integer|min:0',
            'color' => 'required|string|max:7',
            'coordinates' => 'nullable',
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
        $user = Auth::user();

        if ($zone->provider_id !== $user->provider_id) {
            abort(403, 'Unauthorized action.');
        }

        // Clean up relationships
        $zone->providerServices()->delete();

        $zone->delete();
        return redirect()->back()->with('success', 'Zone deleted successfully.');
    }
}
