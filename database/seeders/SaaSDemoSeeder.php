<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use App\Models\Provider;
use App\Models\Zone;
use App\Models\ProviderService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SaaSDemoSeeder extends Seeder
{
    public function run()
    {
        // Define Password for all users
        $password = Hash::make('Secret123!');

        // --- 1. Transport Org (Enjoy Transfers) ---
        // Ensuring it exists
        $transportOrg = Organization::firstOrCreate(
            ['slug' => 'enjoy-transfers'],
            ['name' => 'Enjoy Transfers', 'is_active' => true]
        );
        $this->createUsersForOrg($transportOrg, 'enjoy', $password);

        // Ensure Transport Provider exits
        // Assuming previously seeded data belongs here, identifying by name or creating new.
        $this->createProviderWithServices($transportOrg, 'Enjoy Transfers Fleet', 'transfer');


        // --- 2. Tour Org (Yucatan Tours) ---
        $tourOrg = Organization::firstOrCreate(
            ['slug' => 'yucatan-tours'],
            ['name' => 'Yucatan Tours', 'is_active' => true]
        );
        $this->createUsersForOrg($tourOrg, 'yucatan', $password);

        // Create Tour Provider
        $this->createProviderWithServices($tourOrg, 'Maya Explorers', 'tour');


        // --- 3. Mixed Org (Premium Travel) ---
        $mixedOrg = Organization::firstOrCreate(
            ['slug' => 'premium-travel'],
            ['name' => 'Premium Travel', 'is_active' => true]
        );
        $this->createUsersForOrg($mixedOrg, 'premium', $password);

        // Create Mixed Provider (Doing both)
        $this->createProviderWithServices($mixedOrg, 'Premium All-In-One', 'package');
        // We might want to add specific tour services and transfer services to this one provider

    }

    protected function createUsersForOrg($org, $prefix, $password)
    {
        try {
            // Admin
            User::firstOrCreate(
                ['email' => "admin@{$prefix}.com"],
                [
                    'name' => "Admin {$org->name}",
                    'password' => $password,
                    'organization_id' => $org->id,
                    'role' => 'admin',
                    'phone' => '1234567890'
                ]
            );

            // Supervisor
            User::firstOrCreate(
                ['email' => "supervisor@{$prefix}.com"],
                [
                    'name' => "Sup {$org->name}",
                    'password' => $password,
                    'organization_id' => $org->id,
                    'role' => 'supervisor',
                    'phone' => '1234567890'
                ]
            );

            // Operator
            User::firstOrCreate(
                ['email' => "operator@{$prefix}.com"],
                [
                    'name' => "Op {$org->name}",
                    'password' => $password,
                    'organization_id' => $org->id,
                    'role' => 'operator',
                    'phone' => '1234567890',
                    'operator_status' => 'available'
                ]
            );
        } catch (\Exception $e) {
            $this->command->error("Error creating users for {$org->name}: " . $e->getMessage());
        }
    }

    protected function createProviderWithServices($org, $providerName, $type)
    {
        try {
            $provider = Provider::firstOrCreate(
                ['name' => $providerName, 'organization_id' => $org->id],
                ['email' => strtolower(str_replace(' ', '', $providerName)) . '@example.com', 'is_active' => true]
            );

            // Create a Mock Zone if needed, or reuse first available in Org
            $zone = Zone::where('organization_id', $org->id)->first();
            if (!$zone) {
                try {
                    $zone = Zone::create([
                        'name' => 'Hotel Zone Default',
                        'organization_id' => $org->id,
                        'transfer_time_minutes' => 30,
                        'coordinates' => [
                            ['lat' => 21.1619, 'lng' => -86.8515],
                            ['lat' => 21.1610, 'lng' => -86.8510],
                            ['lat' => 21.1600, 'lng' => -86.8520],
                            ['lat' => 21.1619, 'lng' => -86.8515]
                        ]
                    ]);
                } catch (\Exception $e) {
                    $this->command->error("Error creating Zone: " . $e->getMessage());
                    // Fallback to allow continuing if zone fails (though logic below will fail)
                    // Try creating without transfer_time if it keeps failing, or check migration
                    return;
                }
            }

            if ($type === 'transfer' || $type === 'package') {
                ProviderService::create([
                    'provider_id' => $provider->id,
                    'name' => 'Private Van Transfer',
                    'type' => 'transfer',
                    'cost_net' => 50,
                    'price_public' => 80,
                    'category' => 'standard',
                    'zone_id' => $zone->id
                ]);
            }

            if ($type === 'tour' || $type === 'package') {
                ProviderService::create([
                    'provider_id' => $provider->id,
                    'name' => 'Chichen Itza Deluxe',
                    'type' => 'tour',
                    'cost_net' => 100,
                    'price_public' => 150,
                    'category' => 'vip', // Tours are usually 'standard' or 'vip' too
                    'zone_id' => $zone->id
                ]);
            }
        } catch (\Exception $e) {
            $this->command->error("Error creating Provider/Services for {$org->name}: " . $e->getMessage());
        }
    }
}
