<?php

use App\Models\Organization;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$org = Organization::find(7);
if ($org) {
    echo "Current Mode: " . $org->hosting_mode . "\n";
    $org->hosting_mode = 'subdomain';
    $org->save();
    echo "New Mode: " . $org->fresh()->hosting_mode . "\n";
    echo "Slug: " . $org->slug . "\n";
    echo "Access URL: http://" . $org->slug . ".localhost:8000\n";
} else {
    echo "Organization 7 not found.\n";
}
