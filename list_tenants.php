<?php

use App\Models\Organization;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orgs = Organization::where('id', 7)->get(['id', 'name', 'slug', 'custom_domain', 'hosting_mode']);
foreach ($orgs as $org) {
    echo "ID: {$org->id}\nName: {$org->name}\nSlug: {$org->slug}\nCustom Domain: {$org->custom_domain}\nHosting Mode: {$org->hosting_mode}\n";
}
