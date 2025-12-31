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
            'zones' => Zone::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:zones',
            'priority' => 'required|integer',
            'color' => 'required|string|max:7',
            'coordinates' => 'nullable', // Allow string or JSON
        ]);

        // Ensure coordinates are encoded if passed as array
        if (isset($validated['coordinates']) && is_array($validated['coordinates'])) {
            $validated['coordinates'] = json_encode($validated['coordinates']);
        }

        Zone::create($validated);

        return redirect()->back()->with('success', 'Zone created successfully.');
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:zones,name,' . $zone->id,
            'priority' => 'required|integer',
            'color' => 'required|string|max:7',
            'coordinates' => 'nullable',
        ]);

        if (isset($validated['coordinates']) && is_array($validated['coordinates'])) {
            $validated['coordinates'] = json_encode($validated['coordinates']);
        }

        $zone->update($validated);

        return redirect()->back()->with('success', 'Zone updated successfully.');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();
        return redirect()->back()->with('success', 'Zone deleted successfully.');
    }
}
