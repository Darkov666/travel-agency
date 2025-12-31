<?php

namespace App\Http\Controllers;

use App\Models\Tariff; // Legacy, kept if needed but not used
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

        $destinationName = $request->input('destination');
        $pax = (int) $request->input('pax');
        $type = $request->input('type', 'one_way');

        // Geofencing Check
        $coordinates = $request->input('google_coordinates');
        if ($coordinates && isset($coordinates['lat'], $coordinates['lng'])) {
            $matcher = new \App\Services\ZoneMatcher();
            $matchedZone = $matcher->match((float) $coordinates['lat'], (float) $coordinates['lng']);
            if ($matchedZone) {
                // If precise match found, use its name
                $destinationName = $matchedZone->name;
            }
        }

        // Find or Create Zone (Auto-learning names) - Only find for now to check services
        $zone = \App\Models\Zone::where('name', $destinationName)->first();
        if (!$zone) {
            // If zone doesn't exist, created it but it will have no services
            $zone = \App\Models\Zone::create(['name' => $destinationName]);
        }

        // Fetch Search Results using Provider Logic
        // 1. Get services for this zone
        // 2. Filter active providers
        // 3. Calculate units needed
        $services = \App\Models\ProviderService::where('zone_id', $zone->id)
            ->whereHas('provider', function ($query) {
                $query->where('is_active', true)
                    ->where('provider_type', 'transport'); // Only transport for now
            })
            ->with(['provider', 'service'])
            ->get();

        $results = $services->map(function ($service) use ($pax) {
            // Unit Suggestion Logic
            $unitCapacity = $service->max_pax ?? 10; // Default fallback
            if ($unitCapacity <= 0)
                $unitCapacity = 1; // Prevent div by zero

            $unitsNeeded = ceil($pax / $unitCapacity);

            // If we need multiple units, we show it
            $totalPrice = $service->price_public * $unitsNeeded;
            $totalCapacity = $unitCapacity * $unitsNeeded;

            return [
                'id' => $service->id,
                'id' => $service->id,
                'provider' => [
                    'name' => $service->provider->name,
                    'logo_path' => $service->provider->logo_path,
                ],
                'vehicle_image' => $service->provider->vehicles->where('type', 'van')->first()?->image_path, // Fallback logic or improve later to match service type map
                // Display name: "Van" or custom name, plus unit count if > 1
                // Display name: "Van" or custom name, plus unit count if > 1
                'service_type' => ($service->name ?? $service->service?->title ?? 'Standard Transfer') . ($unitsNeeded > 1 ? " ({$unitsNeeded} Units)" : ""),
                'pax' => $totalCapacity, // Show total capacity of the fleet
                'price' => $totalPrice, // Show total price
                'category' => $service->category, // standard/vip
                'units' => $unitsNeeded, // Meta data
            ];
        })->sortBy('price')->values(); // Sort by cheapest

        return Inertia::render('SearchResults', [
            'results' => $results,
            'searchParams' => $request->all(),
        ]);
    }
}
