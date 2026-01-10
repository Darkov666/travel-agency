<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$slug = 'cancun-sunny';
$org = \App\Models\Organization::where('slug', $slug)->first();

echo "APP_URL: " . config('app.url') . "\n";
if ($org) {
    echo "Organization Found:\n";
    echo "Slug: " . $org->slug . "\n";
    echo "Custom Domain: " . ($org->custom_domain ?? 'NULL') . "\n";
    echo "Hosting Mode: " . $org->hosting_mode . "\n";
    echo "Commercial Name: " . $org->commercial_name . "\n";
} else {
    echo "Organization '$slug' NOT FOUND.\n";
}
