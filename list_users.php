<?php

use App\Models\User;
use App\Models\Organization;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Root Users ---\n";
// Assuming there's a Role model or 'role' column? Or 'is_root'?
// Checking User model structure first might be safer, but let's guess standard fields or check specific users.
// Let's dump all users first to see structure.
$users = User::with('organization')->limit(10)->get();

foreach ($users as $user) {
    echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Org: " . ($user->organization?->name ?? 'None') . " | Role: " . ($user->role ?? 'N/A') . "\n";
}

echo "\n--- Organization 7 (Cancun Sunny) Users ---\n";
$orgUsers = User::where('organization_id', 7)->get();
foreach ($orgUsers as $user) {
    echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Role: " . ($user->role ?? 'N/A') . "\n";
}
