<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;

class ReferentialZonesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            'Cancun',
            'Isla Mujeres',
            'Playa del Carmen',
            'Holbox',
            'Cozumel',
            'Tulum',
            'Puerto Aventuras',
            'Xpuhil',
            'Akumal',
            'Chetumal',
            'Carrillo Puerto',
        ];

        foreach ($zones as $zoneName) {
            // Check if exists global zone (no organization_id)
            $exists = Zone::where('name', $zoneName)
                ->whereNull('organization_id')
                ->exists();

            if (!$exists) {
                Zone::create([
                    'name' => $zoneName,
                    'organization_id' => null, // Global
                    // description, coordinates etc can be null or default
                ]);
            }
        }
    }
}
