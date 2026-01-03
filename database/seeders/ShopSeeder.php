<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Organization;

class ShopSeeder extends Seeder
{
    public function run()
    {
        $org = Organization::first();
        if (!$org) {
            $org = Organization::create([
                'name' => 'Default Org',
                'slug' => 'default',
                'is_active' => true
            ]);
        }

        // Merchandise
        Service::updateOrCreate(
            ['title' => 'Cancun Sunny Cap'],
            [
                'slug' => 'cancun-sunny-cap',
                'organization_id' => $org->id,
                'description' => 'Exclusive branded cap to protect you from the sun in style. High quality cotton.',
                'image' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?q=80&w=800&auto=format&fit=crop',
                'duration_minutes' => 0,
                'is_active' => true,
                'type' => 'merchandise',
                'price' => 25.00,
                'price_mxn' => 450.00,
                'price_usd' => 25.00,
                'features' => ['100% Cotton', 'Adjustable', 'Embroidered Logo']
            ]
        );

        Service::updateOrCreate(
            ['title' => 'Biodegradable Sunscreen'],
            [
                'slug' => 'bio-sunscreen',
                'organization_id' => $org->id,
                'description' => 'Reef safe sunscreen, mandatory for Cenotes and Eco-Parks.',
                'image' => 'https://images.unsplash.com/photo-1526947425960-945c6e72858f?q=80&w=800&auto=format&fit=crop',
                'duration_minutes' => 0,
                'is_active' => true,
                'type' => 'merchandise',
                'price' => 15.00,
                'price_mxn' => 300.00,
                'price_usd' => 15.00,
                'features' => ['Reef Safe', 'SPF 50', 'Water Resistant']
            ]
        );

        // Packages
        Service::updateOrCreate(
            ['title' => 'Xcaret Plus Package'],
            [
                'slug' => 'xcaret-plus-package',
                'organization_id' => $org->id,
                'description' => 'Full day access to Xcaret park with buffet lunch and night show included.',
                'image' => 'https://images.unsplash.com/photo-1534151759604-03738dbb772c?q=80&w=800&auto=format&fit=crop',
                'duration_minutes' => 720,
                'is_active' => true,
                'type' => 'package',
                'price' => 159.00,
                'price_mxn' => 3200.00,
                'price_usd' => 159.00,
                'features' => ['Park Admission', 'Buffet Lunch', 'Snorkel Gear', 'Night Show']
            ]
        );

        $this->command->info('Shop Items Seeded');
    }
}
