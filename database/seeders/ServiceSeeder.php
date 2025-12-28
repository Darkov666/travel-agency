<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Service::create([
            'title' => 'Individual Therapy',
            'description' => 'One-on-one therapy session focused on your personal growth and mental health.',
            'price' => 1.00,
            'price_mxn' => 1.00,
            'price_usd' => 1.00,
            'duration_minutes' => 50,
            'type' => 'individual',
            'is_active' => true,
            'image' => 'https://placehold.co/600x400/F3E5F5/4A148C?text=Individual+Therapy',
        ]);

        \App\Models\Service::create([
            'title' => 'Couples Counseling',
            'description' => 'Therapy sessions designed to improve communication and resolve conflicts in relationships.',
            'price' => 1.00,
            'price_mxn' => 1.00,
            'price_usd' => 1.00,
            'duration_minutes' => 60,
            'type' => 'couple',
            'is_active' => true,
            'image' => 'https://placehold.co/600x400/E1F5FE/01579B?text=Couples+Counseling',
        ]);

        \App\Models\Service::create([
            'title' => 'Family Therapy',
            'description' => 'Sessions involving family members to address specific issues affecting the health and functioning of the family.',
            'price' => 1.00,
            'price_mxn' => 1.00,
            'price_usd' => 1.00,
            'duration_minutes' => 90,
            'type' => 'family',
            'is_active' => true,
            'image' => 'https://placehold.co/600x400/E8F5E9/1B5E20?text=Family+Therapy',
        ]);

        // Special Services
        $specialServices = [
            [
                'title' => 'Capacitación',
                'description' => 'Programas de formación y desarrollo profesional adaptados a sus necesidades.',
                'image' => 'https://placehold.co/600x400/FFF3E0/E65100?text=Capacitacion',
            ],
            [
                'title' => 'Conferencias',
                'description' => 'Charlas magistrales sobre temas relevantes de salud mental y bienestar.',
                'image' => 'https://placehold.co/600x400/FCE4EC/880E4F?text=Conferencias',
            ],
            [
                'title' => 'Talleres',
                'description' => 'Sesiones prácticas e interactivas para el aprendizaje y crecimiento grupal.',
                'image' => 'https://placehold.co/600x400/E8EAF6/1A237E?text=Talleres',
            ],
            [
                'title' => 'Ponencias',
                'description' => 'Presentaciones especializadas en foros académicos o profesionales.',
                'image' => 'https://placehold.co/600x400/F3E5F5/4A148C?text=Ponencias',
            ],
            [
                'title' => 'Pláticas',
                'description' => 'Conversatorios informales y educativos para comunidades o grupos.',
                'image' => 'https://placehold.co/600x400/E0F2F1/004D40?text=Platicas',
            ],
        ];

        foreach ($specialServices as $service) {
            \App\Models\Service::create([
                'title' => $service['title'],
                'description' => $service['description'],
                'price' => 0.00,
                'price_mxn' => 0.00,
                'price_usd' => 0.00,
                'duration_minutes' => 60,
                'type' => 'special',
                'is_active' => true,
                'image' => $service['image'],
            ]);
        }
    }
}
