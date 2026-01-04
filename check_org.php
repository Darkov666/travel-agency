<?php
use App\Models\Organization;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = DB::table('organizations')->count();
echo "Organizations count: $count\n";
if ($count > 0) {
    echo "First Org ID: " . DB::table('organizations')->first()->id . "\n";
}
