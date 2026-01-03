<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cols = ['slug', 'features', 'price_usd', 'price_mxn', 'price'];
foreach ($cols as $col) {
    echo $col . ': ' . (\Illuminate\Support\Facades\Schema::hasColumn('services', $col) ? 'YES' : 'NO') . "\n";
}
