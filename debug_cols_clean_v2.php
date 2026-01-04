<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Columns in 'services' table:\n";
$columns = Schema::getColumnListing('services');
print_r($columns);

echo "\nColumns in 'provider_services' table:\n";
$columns2 = Schema::getColumnListing('provider_services');
print_r($columns2);
