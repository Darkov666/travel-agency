$tenantId = 7;
$tenant = App\Models\Organization::find($tenantId);
echo "Tenant: " . ($tenant->name ?? 'NOT FOUND') . "\n";

echo "\n--- Zones for Org $tenantId ---\n";
$zones = App\Models\Zone::where('organization_id', $tenantId)->get();
foreach ($zones as $z) {
echo "ID: {$z->id} | Name: {$z->name}\n";
}

echo "\n--- Services for Org $tenantId ---\n";
$services = App\Models\Service::where('organization_id', $tenantId)->get();
foreach ($services as $s) {
echo "ID: {$s->id} | Name: {$s->name} | Type: {$s->type}\n";

$psCount = App\Models\ProviderService::where('service_id', $s->id)->count();
echo " -> Linked ProviderServices: $psCount\n";

if ($psCount > 0) {
$providerServices = App\Models\ProviderService::where('service_id', $s->id)->with(['zone', 'provider'])->get();
foreach ($providerServices as $ps) {
echo " - Zone: " . ($ps->zone->name ?? 'NULL') . " (ID: " . $ps->zone_id . ") | Provider: " . ($ps->provider->name ??
'NULL') . "\n";
}
}
}