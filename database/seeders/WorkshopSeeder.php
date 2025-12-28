<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Taller de Manejo de Estrés',
                'description' => 'Aprende técnicas prácticas para reducir el estrés laboral y personal.',
                'price_mxn' => 0, // Request quote
                'duration_minutes' => 120,
                'type' => 'workshop',
                'is_active' => true,
            ],
            [
                'title' => 'Conferencia: Salud Mental en el Trabajo',
                'description' => 'Charla para empresas sobre la importancia del bienestar emocional.',
                'price_mxn' => 0,
                'duration_minutes' => 60,
                'type' => 'conference',
                'is_active' => true,
            ],
            [
                'title' => 'Plática para Padres',
                'description' => 'Orientación sobre crianza respetuosa y límites sanos.',
                'price_mxn' => 0,
                'duration_minutes' => 90,
                'type' => 'talk',
                'is_active' => true,
            ],
            [
                'title' => 'Capacitación para Docentes',
                'description' => 'Herramientas psicológicas para el manejo de grupos.',
                'price_mxn' => 0,
                'duration_minutes' => 180,
                'type' => 'training',
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            try {
                \App\Models\Service::updateOrCreate(
                    ['title' => $service['title']], // Unique by title
                    array_merge($service, [
                        'price' => 0,
                        'price_usd' => 0,
                    ])
                );
            } catch (\Exception $e) {
                echo "Failed to seed service: " . $service['title'] . " - " . $e->getMessage() . "\n";
            }
        }
    }
}
