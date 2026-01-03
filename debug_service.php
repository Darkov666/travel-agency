<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \App\Models\Service::create([
        'title' => 'Test Service ' . rand(1, 999),
        'description' => 'Test',
        'price' => 10.00,
        'type' => 'merchandise',
        'is_active' => true,
    ]);
    echo "Success!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
