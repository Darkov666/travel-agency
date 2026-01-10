<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orgs = \App\Models\Organization::all(['id', 'name', 'commercial_name']);
foreach ($orgs as $org) {
    echo "ID: {$org->id} | Name: {$org->name} | Commercial: {$org->commercial_name}\n";
}
