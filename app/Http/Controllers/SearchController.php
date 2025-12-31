<?php

namespace App\Http\Controllers;

use App\Models\Tariff;
use App\Models\Zone;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'destination' => 'required|string',
            'pax' => 'required|integer|min:1',
            'adults' => 'nullable|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'type' => 'nullable|string|in:one_way,round_trip',
            'date' => 'nullable|date',
            'return_date' => 'nullable|required_if:type,round_trip|date|after_or_equal:date',
        ]);

        $destination = $request->input('destination');
        $pax = (int) $request->input('pax');
        $type = $request->input('type', 'one_way');

        // Geofencing Check
        $coordinates = $request->input('google_coordinates');
        if ($coordinates && isset($coordinates['lat'], $coordinates['lng'])) {
            $matcher = new \App\Services\ZoneMatcher();
            $matchedZone = $matcher->match((float) $coordinates['lat'], (float) $coordinates['lng']);

            if ($matchedZone) {
                // Override user selection with precise geofenced zone
                $destination = $matchedZone->name;
            }
        }

        // Auto-save new zones (only if not found by matcher and provided by user)
        if ($destination && !\App\Models\Zone::where('name', $destination)->exists()) {
            \App\Models\Zone::create(['name' => $destination]);
        }

        // Fetch tariffs for the zone
        // Since 'pax' is a string "1 a 8", we fetch all for the zone and filter in PHP
        // Optimization: Normalize database columns in future
        $tariffs = Tariff::with('provider')
            ->where('zone', $destination)
            ->get()
            ->filter(function ($tariff) use ($pax) {
                // Parse "X a Y"
                if (preg_match('/(\d+)\s*a\s*(\d+)/', $tariff->pax, $matches)) {
                    $min = (int) $matches[1];
                    $max = (int) $matches[2];
                    return $pax >= $min && $pax <= $max;
                }
                return false;
            })->values();

        return Inertia::render('SearchResults', [
            'results' => $tariffs,
            'searchParams' => $request->all(),
        ]);
    }
}
