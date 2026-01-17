<?php

use App\Models\Organization;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$org = Organization::find(7);
if ($org) {
    echo "Current Slug: " . $org->slug . "\n";
    echo "Current Mode: " . $org->hosting_mode . "\n";

    // Update to requested values
    $org->slug = 'cancunsunny';
    $org->hosting_mode = 'domain';
    // Ensure custom domain is set correctly (assuming cancunsunny.com based on previous context)
    // If it was already set, this just confirms it.
    if (empty($org->custom_domain) || $org->custom_domain !== 'cancunsunny.com') {
        $org->custom_domain = 'cancunsunny.com';
    }

    $org->save();

    $org = $org->fresh();
    echo "--- Updated ---\n";
    echo "New Slug: " . $org->slug . "\n";
    echo "New Mode: " . $org->hosting_mode . "\n";
    echo "Custom Domain: " . $org->custom_domain . "\n";
} else {
    echo "Organization 7 not found.\n";
}
