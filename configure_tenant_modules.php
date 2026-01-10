<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$slug = 'cancun-sunny';
$org = \App\Models\Organization::where('slug', $slug)->first();

if ($org) {
    echo "Current Settings: " . json_encode($org->settings) . "\n";

    // Set modules to Transport ONLY
    $settings = $org->settings ?? [];
    $settings['modules'] = ['transport'];

    $org->settings = $settings;
    $org->save();

    echo "Updated Settings: " . json_encode($org->settings) . "\n";
} else {
    echo "Organization '$slug' not found.\n";
}
