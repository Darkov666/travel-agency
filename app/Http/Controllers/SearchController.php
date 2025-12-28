<?php

namespace App\Http\Controllers;

use App\Models\Tariff;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'destination' => 'required|string',
            'pax' => 'required|integer|min:1',
            'type' => 'nullable|string|in:one_way,round_trip',
            'date' => 'nullable|date',
        ]);

        $destination = $request->input('destination');
        $pax = (int) $request->input('pax');
        $type = $request->input('type', 'one_way');

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
