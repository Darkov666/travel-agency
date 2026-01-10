<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orgId = 7; // Cancun Sunny

echo "Checking Services for Org ID: $orgId\n";

$services = \App\Models\Service::where('organization_id', $orgId)->get();
echo "Found " . $services->count() . " Services.\n";
foreach ($services as $s) {
    echo "- Service: {$s->id} | {$s->title} | Type: {$s->type} | Active: {$s->is_active}\n";
}

echo "\nChecking ProviderServices linked to these Services:\n";
foreach ($services as $s) {
    $ps = \App\Models\ProviderService::where('service_id', $s->id)->get();
    echo "  Service {$s->id} has " . $ps->count() . " ProviderServices.\n";
    foreach ($ps as $p) {
        echo "    - PS ID: {$p->id} | Zone: {$p->zone_id} | Price: {$p->price_public}\n";
    }
}

echo "\nChecking Zones for Org ID: $orgId\n";
$zones = \App\Models\Zone::where('organization_id', $orgId)->get();
echo "Found " . $zones->count() . " Zones.\n";
foreach ($zones as $z) {
    echo "- Zone: {$z->id} | {$z->name}\n";
}
