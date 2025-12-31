<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        // Defined Polygons (Simplified approx coordinates)
        $zones = [
            'Cancun Hotel Zone' => [
                ['lat' => 21.146, 'lng' => -86.778],
                ['lat' => 21.146, 'lng' => -86.756],
                ['lat' => 21.037, 'lng' => -86.780],
                ['lat' => 21.025, 'lng' => -86.804],
                ['lat' => 21.029, 'lng' => -86.828],
                ['lat' => 21.155, 'lng' => -86.806],
            ],
            'Playa Mujeres' => [
                ['lat' => 21.280, 'lng' => -86.805],
                ['lat' => 21.280, 'lng' => -86.780],
                ['lat' => 21.215, 'lng' => -86.780],
                ['lat' => 21.215, 'lng' => -86.820],
            ],
            'Puerto Morelos' => [
                ['lat' => 20.890, 'lng' => -86.910],
                ['lat' => 20.890, 'lng' => -86.850],
                ['lat' => 20.810, 'lng' => -86.850],
                ['lat' => 20.810, 'lng' => -86.930],
            ],
            'Playa del Carmen' => [
                ['lat' => 20.686, 'lng' => -87.030],
                ['lat' => 20.686, 'lng' => -87.086],
                ['lat' => 20.605, 'lng' => -87.100],
                ['lat' => 20.605, 'lng' => -87.050],
            ],
            'Puerto Aventuras' => [
                ['lat' => 20.520, 'lng' => -87.200],
                ['lat' => 20.520, 'lng' => -87.240],
                ['lat' => 20.480, 'lng' => -87.240],
                ['lat' => 20.480, 'lng' => -87.200],
            ],
            'Akumal' => [
                ['lat' => 20.420, 'lng' => -87.290],
                ['lat' => 20.420, 'lng' => -87.320],
                ['lat' => 20.380, 'lng' => -87.320],
                ['lat' => 20.380, 'lng' => -87.290],
            ],
            'Tulum' => [ // Covers town and hotel zone
                ['lat' => 20.250, 'lng' => -87.350],
                ['lat' => 20.250, 'lng' => -87.500],
                ['lat' => 20.120, 'lng' => -87.500],
                ['lat' => 20.120, 'lng' => -87.400],
                ['lat' => 20.130, 'lng' => -87.350],
            ],
        ];

        foreach ($zones as $name => $coordinates) {
            Zone::updateOrCreate(
                ['name' => $name],
                ['coordinates' => json_encode($coordinates)]
            );
        }
    }
}
