<?php
$users = App\Models\User::whereHas('organization', function ($q) {
    $q->where('slug', 'cancun-sunny');
})->get();

echo "Users for Cancun Sunny:\n";
foreach ($users as $u) {
    echo "ID: $u->id, Name: $u->name, Role: $u->role, Org: " . ($u->organization->name ?? 'None') . "\n";
}

$root = App\Models\User::where('role', 'root')->first();
echo "\nRoot User: " . ($root->name ?? 'None') . ", Org: " . ($root->organization_id ?? 'NULL') . "\n";
