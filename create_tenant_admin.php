<?php
$org = App\Models\Organization::where('slug', 'cancun-sunny')->first();

if ($org) {
    // Check if admin exists
    $user = App\Models\User::where('email', 'admin@cancunsunny.com')->first();

    if (!$user) {
        $user = App\Models\User::create([
            'name' => 'Cancun Sunny Admin',
            'email' => 'admin@cancunsunny.com',
            'password' => bcrypt('password'),
            'role' => 'admin', // Tenant Admin
            'organization_id' => $org->id,
        ]);
        echo "Created User: admin@cancunsunny.com\n";
    } else {
        // Ensure role and org are correct
        $user->role = 'admin';
        $user->organization_id = $org->id;
        $user->save();
        echo "Updated User: admin@cancunsunny.com\n";
    }
} else {
    echo "Organization not found.\n";
}
