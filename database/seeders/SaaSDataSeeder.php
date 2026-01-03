<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use App\Models\Provider;
use App\Models\Zone;
use App\Models\ReservationItem; // Using Items as they are the main service unit, but Reservation table also updated
use Illuminate\Support\Facades\DB;

class SaaSDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Default Organization
        $org = Organization::firstOrCreate(
            ['slug' => 'enjoy-transfers'],
            [
                'name' => 'Enjoy Transfers',
                'is_active' => true,
                'settings' => ['currency' => 'USD', 'theme' => 'light']
            ]
        );

        $this->command->info("Organization: {$org->name} ({$org->id})");

        // 2. Assign existing Data to this Org

        // Users
        $usersCount = User::whereNull('organization_id')->update(['organization_id' => $org->id]);
        $this->command->info("Assigned {$usersCount} users to default org.");

        // Providers
        $providersCount = Provider::whereNull('organization_id')->update(['organization_id' => $org->id]);
        $this->command->info("Assigned {$providersCount} providers.");

        // Zones
        $zonesCount = Zone::whereNull('organization_id')->update(['organization_id' => $org->id]);
        $this->command->info("Assigned {$zonesCount} zones.");

        // Reservations (Using DB facade for reservations table as Model might not have it fillable yet or we want to be raw)
        $reservationsCount = DB::table('reservations')->whereNull('organization_id')->update(['organization_id' => $org->id]);
        $this->command->info("Assigned {$reservationsCount} reservations.");

        // Vehicles
        $vehiclesCount = DB::table('vehicles')->whereNull('organization_id')->update(['organization_id' => $org->id]);
        $this->command->info("Assigned {$vehiclesCount} vehicles.");

        // 3. Set Root User
        // Try to find the user 'root' or similar, or just pick the first one and make it root for dev purposes if no specific user exists
        // User requested 'root' profile.

        $rootUser = User::where('email', 'root@example.com')->first();
        if (!$rootUser) {
            $rootUser = User::first();
        }

        if ($rootUser) {
            $rootUser->role = 'root';
            // Root doesn't necessarily need organization logic, but for now let's keep it assigned to one or null?
            // "root... puede ver el contenido de todos...".
            // If we leave organization_id on root, they are "based" there but have global access.
            $rootUser->save();
            $this->command->info("User {$rootUser->email} set as ROOT.");
        }
    }
}
