<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Package::create([
            'title' => 'Starter Pack',
            'description' => 'A bundle of 3 individual therapy sessions.',
            'price' => 220.00,
            'session_count' => 3,
            'is_active' => true,
            'image' => 'https://images.unsplash.com/photo-1519834785169-98be25ec3f84?q=80&w=800&auto=format&fit=crop',
            'video_url' => null,
        ]);

        \App\Models\Package::create([
            'title' => 'Transformation Journey',
            'description' => 'A comprehensive package of 10 sessions for long-term progress.',
            'price' => 700.00,
            'session_count' => 10,
            'is_active' => true,
            'image' => 'https://images.unsplash.com/photo-1499209974431-9dddcece7f88?q=80&w=800&auto=format&fit=crop',
            'video_url' => 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExbDN6eW56eW56eW56eW56eW56eW56eW56eW56eW56eW56eSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/3o7TKr3nzbh5WgCFxe/giphy.gif',
        ]);
    }
}
