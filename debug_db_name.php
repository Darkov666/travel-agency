<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "DB Name: " . DB::connection()->getDatabaseName() . "\n";
echo "DB Host: " . DB::getConfig('host') . "\n";

$s = DB::table('services')->where('id', 101)->first();
if ($s) {
    echo "Service 101 Found: " . $s->title . "\n";
} else {
    echo "Service 101 NOT Found.\n";
}
