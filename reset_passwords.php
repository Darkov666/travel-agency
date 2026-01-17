<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$usersToReset = [
    'root' => 1, // ID 1 is usually root
    'cancun_admin' => 23 // ID 23 appeared to be the Cancun Sunny user
];

foreach ($usersToReset as $role => $id) {
    $user = User::find($id);
    if ($user) {
        $user->password = Hash::make('password');
        $user->save();
        echo "Reset password for {$role}: {$user->email} (ID: {$user->id}) to 'password'\n";
    } else {
        echo "User ID {$id} not found for {$role}\n";
    }
}
