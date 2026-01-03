<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\User;
use App\Models\Provider;
use App\Models\Reservation;
use App\Models\ReservationItem;
use App\Models\ProviderService;
use App\Models\Zone;
use Carbon\Carbon;

class SaaSReservationSeeder extends Seeder
{
    public function run()
    {
        $orgs = ['enjoy-transfers', 'yucatan-tours', 'premium-travel'];

        foreach ($orgs as $slug) {
            $org = Organization::where('slug', $slug)->first();
            if (!$org)
                continue;

            $this->command->info("Seeding Reservations for {$org->name}...");

            // Get a default provider for this org to assign some tasks
            $provider = Provider::where('organization_id', $org->id)->first();
            if (!$provider) {
                $this->command->error("No provider found for {$org->name}");
                continue;
            }

            $service = ProviderService::where('provider_id', $provider->id)->first();
            if (!$service) {
                // Create dummy service if missing
                $service = ProviderService::create([
                    'provider_id' => $provider->id,
                    'name' => 'Fallback Service',
                    'type' => 'transfer',
                    'cost_net' => 50,
                    'price_public' => 80,
                    'category' => 'standard',
                    'zone_id' => Zone::firstOrCreate(['organization_id' => $org->id, 'name' => 'Default'], ['coordinates' => [], 'transfer_time_minutes' => 30])->id,
                    'is_active' => true
                ]);
            }

            // 1. Create 3 Tasks for TODAY (Mix of Pending and Assigned)
            $this->createReservation($org, $provider, $service, Carbon::today()->setTime(10, 0), 'assigned', 'pending'); // Assigned, not started
            $this->createReservation($org, $provider, $service, Carbon::today()->setTime(14, 0), 'assigned', 'en_camino'); // In progress
            $this->createReservation($org, null, $service, Carbon::today()->setTime(16, 0), 'pending', null); // Unassigned

            // 2. Create 2 Tasks for TOMORROW
            $this->createReservation($org, $provider, $service, Carbon::tomorrow()->setTime(0, 0), 'assigned', 'pending');
            $this->createReservation($org, $provider, $service, Carbon::tomorrow()->setTime(12, 0), 'assigned', 'pending');
        }
    }

    protected function createReservation($org, $provider, $service, $date, $vendorStatus, $opStatus)
    {
        // Create Mock Layout User if needed, or just null
        // We'll just create a dummy "Client" user if one doesn't exist for the org
        // actually Reservation doesn't strictly need a user_id if we make it nullable, 
        // but our schema might require it. Let's pick the Admin as the 'booker' for simplicity 
        // or create a dummy client.

        $user = User::where('organization_id', $org->id)->where('role', 'admin')->first();

        if (!$service) {
            throw new \Exception("Service is required for reservation item");
        }

        try {
            $res = Reservation::create([
                'organization_id' => $org->id,
                'user_id' => $user ? $user->id : null,
                'booking_ref' => strtoupper(substr($org->slug, 0, 3)) . '-' . rand(1000, 9999),
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'subtotal' => 100,
                'tax' => 0,
                'total_amount' => 100, // Correct column name
                'currency' => 'USD',
                // Contact Info
                'contact_name' => 'Demo',
                'contact_surname' => 'User',
                'contact_email' => $user ? $user->email : 'demo@example.com',
                'contact_phone' => '555-0000',
                'contact_nationality' => 'MX'
            ]);

            ReservationItem::create([
                'reservation_id' => $res->id,
                'provider_service_id' => $service->id,
                'service_name' => $service->name,
                'zone_id' => $service->zone_id,
                'zone_name' => 'Hotel Zone', // Could fetch from zone
                'date' => $date->format('Y-m-d'),
                'time' => $date->format('H:i'),
                'adults' => 2,
                'children' => 0,
                'infants' => 0,
                'pax' => 2,
                'quantity' => 1,
                'unit_price' => 100,
                'total_price' => 100,
                'total' => 100,
                'cost' => $service->cost_net,
                'vendor_status' => $provider ? $vendorStatus : 'pending',
                'assigned_provider_id' => $provider ? $provider->id : null,
                'operational_status' => $opStatus,
                'organization_id' => $org->id
            ]);
        } catch (\Throwable $e) {
            file_put_contents('seeder_error.txt', $e->getMessage() . "\n" . $e->getTraceAsString(), FILE_APPEND);
            $this->command->error("Error creating reservation ({$org->name}): " . $e->getMessage());
        }
    }
}
