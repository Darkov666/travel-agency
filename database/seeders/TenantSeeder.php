<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Organization
        $org = \App\Models\Organization::firstOrCreate(
            ['slug' => 'cancun-sunny'],
            [
                'name' => 'Cancun Sunny',
                'custom_domain' => 'cancunsunny.com',
                'hosting_mode' => 'domain',
                'representative_email' => 'admin@cancunsunny.com',
                'is_active' => true,
                'settings' => ['primary_color' => '#f59e0b', 'secondary_color' => '#14b8a6'],
            ]
        );

        // 2. Create Users
        $password = bcrypt('password'); // Default testing password

        // Tenant Admin
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@cancunsunny.com'],
            [
                'name' => 'Cancun Sunny Admin',
                'password' => $password,
                'role' => 'admin',
                'organization_id' => $org->id,
                'gender' => 'other',
                'phone' => '1234567890',
            ]
        );

        // Supervisor (Ops)
        $ops = \App\Models\User::firstOrCreate(
            ['email' => 'ops@cancunsunny.com'],
            [
                'name' => 'Cancun Ops Manager',
                'password' => $password,
                'role' => 'supervisor',
                'organization_id' => $org->id,
                'gender' => 'other',
                'phone' => '1234567890',
            ]
        );

        // Driver
        $driver = \App\Models\User::firstOrCreate(
            ['email' => 'driver@cancunsunny.com'],
            [
                'name' => 'Carlos Driver',
                'password' => $password,
                'role' => 'driver',
                'organization_id' => $org->id,
                'gender' => 'male',
                'phone' => '1234567890',
                'provider_id' => null, // Driver directly employed by tenant or assign to a provider? Assuming tenant employee for now.
            ]
        );

        $this->command->info('Cancun Sunny tenant seeded successfully!');
        $this->command->info('Admin: admin@cancunsunny.com');
        $this->command->info('Ops: ops@cancunsunny.com');
        $this->command->info('Driver: driver@cancunsunny.com');
    }
}
