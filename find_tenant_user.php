<?php
$org = App\Models\Organization::where('slug', 'cancun-sunny')->first();
if ($org) {
    $users = App\Models\User::where('organization_id', $org->id)->get();
    if ($users->isEmpty()) {
        echo "No users found for Cancun Sunny.\n";
    } else {
        foreach ($users as $u) {
            echo "User found: Email: {$u->email}, Role: {$u->role}\n";
        }
    }
} else {
    echo "Organization not found.\n";
}
