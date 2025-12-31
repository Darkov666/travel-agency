<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        // Defined Polygons (Simplified approx coordinates)
        // Transfer times based on Cancun Airport (Approx)
        $zones = [
            'Cancun Hotel Zone' => [
                'coords' => [
                    ['lat' => 21.146, 'lng' => -86.778],
                    ['lat' => 21.146, 'lng' => -86.756],
                    ['lat' => 21.037, 'lng' => -86.780],
                    ['lat' => 21.025, 'lng' => -86.804],
                    ['lat' => 21.029, 'lng' => -86.828],
                    ['lat' => 21.155, 'lng' => -86.806],
                ],
                'time' => 30
            ],
            'Playa Mujeres' => [
                'coords' => [
                    ['lat' => 21.280, 'lng' => -86.805],
                    ['lat' => 21.280, 'lng' => -86.780],
                    ['lat' => 21.215, 'lng' => -86.780],
                    ['lat' => 21.215, 'lng' => -86.820],
                ],
                'time' => 45
            ],
            'Puerto Morelos' => [
                'coords' => [
                    ['lat' => 20.890, 'lng' => -86.910],
                    ['lat' => 20.890, 'lng' => -86.850],
                    ['lat' => 20.810, 'lng' => -86.850],
                    ['lat' => 20.810, 'lng' => -86.930],
                ],
                'time' => 25
            ],
            'Playa del Carmen' => [
                'coords' => [
                    ['lat' => 20.686, 'lng' => -87.030],
                    ['lat' => 20.686, 'lng' => -87.086],
                    ['lat' => 20.605, 'lng' => -87.100],
                    ['lat' => 20.605, 'lng' => -87.050],
                ],
                'time' => 50
            ],
            'Puerto Aventuras' => [
                'coords' => [
                    ['lat' => 20.520, 'lng' => -87.200],
                    ['lat' => 20.520, 'lng' => -87.240],
                    ['lat' => 20.480, 'lng' => -87.240],
                    ['lat' => 20.480, 'lng' => -87.200],
                ],
                'time' => 75
            ],
            'Akumal' => [
                'coords' => [
                    ['lat' => 20.420, 'lng' => -87.290],
                    ['lat' => 20.420, 'lng' => -87.320],
                    ['lat' => 20.380, 'lng' => -87.320],
                    ['lat' => 20.380, 'lng' => -87.290],
                ],
                'time' => 85
            ],
            'Tulum' => [ // Covers town and hotel zone
                'coords' => [
                    ['lat' => 20.250, 'lng' => -87.350],
                    ['lat' => 20.250, 'lng' => -87.500],
                    ['lat' => 20.120, 'lng' => -87.500],
                    ['lat' => 20.120, 'lng' => -87.400],
                    ['lat' => 20.130, 'lng' => -87.350],
                ],
                'time' => 110
            ],
        ];

        foreach ($zones as $name => $data) {
            // Use firstOrCreate to avoid overwriting user changes to these zones
            Zone::updateOrCreate(
                ['name' => $name],
                [
                    'coordinates' => $data['coords'],
                    'transfer_time_minutes' => $data['time'], // Updated
                    'priority' => 0,
                    'color' => '#3b82f6'
                ]
            );
        }
    }
}
