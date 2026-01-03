<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\Provider;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Organizations ---\n";
foreach (Organization::all() as $org) {
    echo "- {$org->name} (ID: {$org->id})\n";
}

echo "\n--- Users ---\n";
foreach (User::with('organization')->get() as $user) {
    echo "- {$user->email} ({$user->role}) - Org: " . ($user->organization->name ?? 'None') . "\n";
}

echo "\n--- Providers ---\n";
foreach (Provider::with('providerServices')->get() as $provider) {
    echo "- {$provider->name} (Org: {$provider->organization_id}) - Services: " . $provider->providerServices->count() . "\n";
    foreach ($provider->providerServices as $service) {
        echo "  * {$service->name} ({$service->type})\n";
    }
}
