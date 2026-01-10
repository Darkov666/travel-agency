<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$org = \App\Models\Organization::find(7);
if ($org) {
    echo "Current Commercial Name: " . ($org->commercial_name ?? 'NULL') . "\n";
    echo "Current Name: " . ($org->name ?? 'NULL') . "\n";

    $org->commercial_name = 'Cancun Sunny';
    $org->save();

    echo "Updated Commercial Name to 'Cancun Sunny'.\n";
} else {
    echo "Org ID 7 not found.\n";
}
