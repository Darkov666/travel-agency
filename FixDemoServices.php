<?php

use App\Models\Organization;
use App\Models\Provider;
use App\Models\Zone;
use App\Models\ProviderService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function ensureService($orgSlug, $providerName, $type)
{
    echo "Checking {$orgSlug}...\n";
    $org = Organization::where('slug', $orgSlug)->first();
    if (!$org) {
        echo "Org {$orgSlug} not found!\n";
        return;
    }

    $provider = Provider::where('organization_id', $org->id)->where('name', $providerName)->first();
    if (!$provider) {
        echo "Provider {$providerName} not found, skipping.\n";
        return;
    }

    $zone = Zone::where('organization_id', $org->id)->first();
    if (!$zone) {
        echo "Creating Zone for {$org->name}...\n";
        $zone = Zone::create([
            'name' => 'Default Zone',
            'organization_id' => $org->id,
            'transfer_time_minutes' => 30,
            'coordinates' => [
                ['lat' => 21.1619, 'lng' => -86.8515],
                ['lat' => 21.1610, 'lng' => -86.8510],
                ['lat' => 21.1600, 'lng' => -86.8520],
                ['lat' => 21.1619, 'lng' => -86.8515]
            ]
        ]);
    }

    // Transfers
    if ($type === 'transfer' || $type === 'package') {
        if (!$provider->providerServices()->where('type', 'transfer')->exists()) {
            echo "Adding Transfer Service...\n";
            ProviderService::create([
                'provider_id' => $provider->id,
                'name' => 'Private Van Transfer',
                'type' => 'transfer',
                'cost_net' => 50,
                'price_public' => 80,
                'category' => 'standard',
                'zone_id' => $zone->id
            ]);
        }
    }

    // Tours
    if ($type === 'tour' || $type === 'package') {
        if (!$provider->providerServices()->where('type', 'tour')->exists()) {
            echo "Adding Tour Service...\n";
            ProviderService::create([
                'provider_id' => $provider->id,
                'name' => 'Chichen Itza Deluxe',
                'type' => 'tour',
                'cost_net' => 100,
                'price_public' => 150,
                'category' => 'vip',
                'zone_id' => $zone->id
            ]);
        }
    }
}

// Run fixes
ensureService('enjoy-transfers', 'Enjoy Transfers Fleet', 'transfer');
ensureService('yucatan-tours', 'Maya Explorers', 'tour');
ensureService('premium-travel', 'Premium All-In-One', 'package');

echo "Done.\n";
