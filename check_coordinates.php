<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$zones = \App\Models\Zone::where('organization_id', 7)->get();
foreach ($zones as $z) {
    echo "ID: {$z->id} | Name: {$z->name} | Coords Length: " . strlen($z->coordinates ?? '') . "\n";
}
