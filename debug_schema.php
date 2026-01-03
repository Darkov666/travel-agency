<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = \Illuminate\Support\Facades\Schema::getColumnListing('services');
print_r($columns);

echo "\nOrganization Columns:\n";
print_r(\Illuminate\Support\Facades\Schema::getColumnListing('organizations'));

try {
    \App\Models\Organization::create([
        'name' => 'Test Org ' . time(),
        'slug' => 'test-org-' . time(),
        'is_active' => true,
        // 'hosting_mode' => 'subdomain' // Check if required
    ]);
    echo "\nOrg Created successfully.";
} catch (\Exception $e) {
    echo "\nOrg Creation Failed: " . $e->getMessage();
}
