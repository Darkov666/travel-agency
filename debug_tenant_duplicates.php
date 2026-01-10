<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$domain = 'cancunsunny.com';
$orgs = \App\Models\Organization::where('custom_domain', $domain)->get();

echo "Found " . $orgs->count() . " organizations for domain '$domain':\n";
foreach ($orgs as $o) {
    echo "ID: " . $o->id . " | Name: " . ($o->commercial_name ?? 'NULL') . " | Slug: " . $o->slug . " | Mode: " . $o->hosting_mode . "\n";
}
