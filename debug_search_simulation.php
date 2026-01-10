<?php

use App\Models\Organization;
use App\Models\Zone;
use App\Models\ProviderService;
use Illuminate\Support\Facades\DB;

// Simulate Request Parameters
$tenantSlug = 'cancun-sunny'; // Assuming this is the slug
$destinationName = 'Moon Palace';
// $destinationName = 'Zona Hotelera'; 
$pax = 2;

echo "--- Debugging Search for Tenant: $tenantSlug ---\n";

// 1. Resolve Tenant
$tenant = Organization::where('slug', $tenantSlug)->first();
if (!$tenant) {
    // Try by ID 7
    $tenant = Organization::find(7);
}

if (!$tenant) {
    die("Tenant not found.\n");
}
echo "Tenant ID: " . $tenant->id . " Name: " . $tenant->name . "\n";

// 2. Simulate SearchController Zone Logic
$zone = Zone::where('organization_id', $tenant->id)
    ->where(function ($query) use ($destinationName) {
        $query->where('name', 'LIKE', $destinationName) // Strict LIKE
            ->orWhere('name', 'LIKE', '%' . $destinationName . '%'); // Broad LIKE
    })->first();

if (!$zone) {
    echo "[!] No exact zone match for '$destinationName'.\n";
    // Try the "Moon Palace" alias logic from SearchWidget (frontend) 
    // BUT SearchController (backend) doesn't have that alias logic unless I added it?
    // Start of my previous fix check:
    // I modified SearchController to use 'LIKE'.

    // Let's try searching for "Zona Hotelera" as if the frontend did the mapping
    $mappedDestination = 'Zona Hotelera';
    echo "Trying mapped destination: '$mappedDestination'...\n";

    $zone = Zone::where('organization_id', $tenant->id)
        ->where('name', 'LIKE', '%' . $mappedDestination . '%')
        ->first();
}

if (!$zone) {
    die("[!!!] Zone still not found. Available Zones for Tenant:\n" . Zone::where('organization_id', $tenant->id)->pluck('name')->join(', ') . "\n");
}

echo "Found Zone: " . $zone->name . " (ID: " . $zone->id . ")\n";

// 3. Find Provider Services
// Logic from SearchController:
// $results = ProviderService::where('zone_id', $zone->id)...
//  ->whereHas('service', function($q) use ($tenant) { $q->where('organization_id', $tenant->id); })

$services = ProviderService::where('zone_id', $zone->id)
    ->with(['service', 'provider'])
    ->get();

echo "Found " . $services->count() . " ProviderServices for this Zone.\n";

foreach ($services as $ps) {
    echo "- Service: " . ($ps->service ? $ps->service->name : 'N/A') .
        " | Provider: " . ($ps->provider ? $ps->provider->name : 'N/A') .
        " | Price: " . $ps->price .
        " | Service Org ID: " . ($ps->service ? $ps->service->organization_id : 'N/A') . "\n";

    if ($ps->service && $ps->service->organization_id != $tenant->id) {
        echo "  [!] Mismatch: Service Org ID " . $ps->service->organization_id . " != Tenant ID " . $tenant->id . "\n";
    }
}
