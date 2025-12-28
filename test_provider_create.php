<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Provider;

try {
    $p = Provider::create(['name' => 'Test', 'is_inhouse' => false, 'is_default' => false, 'is_active' => true]);
    echo 'Created Provider ID: ' . $p->id . PHP_EOL;
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
