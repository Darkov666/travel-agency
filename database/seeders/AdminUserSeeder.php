<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'darkov666@darkov.com'],
            [
                'name' => 'Darkov',
                'password' => Hash::make('password'),
                'role' => 'admin', // or 'root' if you want super admin, but user asked for 'admin' permissions
                'email_verified_at' => now(),
            ]
        );
    }
}
