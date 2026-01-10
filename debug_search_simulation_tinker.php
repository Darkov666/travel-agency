$tenantSlug = 'cancun-sunny';
$destinationName = 'Moon Palace';
$pax = 2;

echo "--- Debugging Search for Tenant: $tenantSlug ---\n";

$tenant = App\Models\Organization::where('slug', $tenantSlug)->first() ?? App\Models\Organization::find(7);

if (!$tenant) {
echo "Tenant not found.\n";
return;
}

echo "Tenant: " . $tenant->name . " (ID: " . $tenant->id . ")\n";

// Backend Search Logic Simulation
// 1. Try finding Zone (using LIKE)
$zone = App\Models\Zone::where('organization_id', $tenant->id)
->where(function ($query) use ($destinationName) {
$query->where('name', 'LIKE', $destinationName)
->orWhere('name', 'LIKE', '%' . $destinationName . '%');
})->first();

if (!$zone) {
echo "[!] No direct match for '$destinationName'.\n";
// Simulate Alias Mapping from Frontend
$mapped = 'Zona Hotelera';
echo "Trying mapped: '$mapped'...\n";
$zone = App\Models\Zone::where('organization_id', $tenant->id)
->where('name', 'LIKE', '%' . $mapped . '%')
->first();
}

if (!$zone) {
$all = App\Models\Zone::where('organization_id', $tenant->id)->pluck('name')->join(', ');
echo "Zone NOT FOUND. Available: $all\n";
} else {
echo "Found Zone: " . $zone->name . " (ID: " . $zone->id . ")\n";

// 2. Find Services
$services = App\Models\ProviderService::where('zone_id', $zone->id)
->whereHas('provider', function ($q) { $q->where('is_active', true); })
->whereHas('service', function ($q) use ($tenant) { $q->where('organization_id', $tenant->id); })
->with(['service', 'provider'])
->get();

echo "Services Found: " . $services->count() . "\n";
foreach ($services as $ps) {
echo "- " . $ps->service->name . " ($" . $ps->price . ") by " . $ps->provider->name . "\n";
}
}