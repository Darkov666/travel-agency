<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\BlogPost;
use App\Models\BlogTopic;
use App\Models\User;

class TravelContentSeeder extends Seeder
{
    public function run(): void
    {
        // Disable mass assignment protection
        Service::unguard();
        BlogPost::unguard();
        BlogTopic::unguard();

        // 1. Clear existing content
        // We use delete() instead of truncate() to avoid foreign key issues if strict mode is on 
        // or just disable foreign key checks
        DB::statement('PRAGMA foreign_keys = OFF;'); // If SQLite
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // If MySQL

        try {
            DB::table('services')->truncate();
        } catch (\Exception $e) {
            DB::table('services')->delete();
        }

        try {
            DB::table('blog_posts')->truncate();
        } catch (\Exception $e) {
            DB::table('blog_posts')->delete();
        }

        try {
            DB::table('blog_topics')->truncate();
        } catch (\Exception $e) {
            DB::table('blog_topics')->delete();
        }

        // 2. Ensure Admin User & Organization
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@cancunsunny.com',
                'password' => bcrypt('password'),
            ]);
        }

        $org = \App\Models\Organization::first();
        if (!$org) {
            $org = \App\Models\Organization::create([
                'name' => 'Default Org',
                'slug' => 'default',
                'is_active' => true
            ]);
        }

        // 3. Seed Services (Transfers & Tours)
        $services = [
            [
                'title' => 'Private Airport Transfer - Hotel Zone',
                'slug' => 'private-transfer-hotel-zone',
                'description' => 'Direct, private transportation from Cancun Airport to your hotel in the Hotel Zone. No waiting, no stops.',
                'price' => 45.00,
                'price_usd' => 45.00,
                'price_mxn' => 900.00,
                'duration_minutes' => 30,
                'type' => 'transfer',
                'image' => 'https://images.unsplash.com/photo-1549488497-6523cc096535?q=80&w=800',
                'is_active' => true,
                'features' => ['Private Vehicle', 'Air Conditioning', 'Bilingual Driver', 'Flight Monitoring']
            ],
            [
                'title' => 'Private Airport Transfer - Playa del Carmen',
                'slug' => 'private-transfer-playa-del-carmen',
                'description' => 'Comfortable private van for your group to Playa del Carmen. Refreshments included.',
                'price' => 75.00,
                'price_usd' => 75.00,
                'price_mxn' => 1500.00,
                'duration_minutes' => 60,
                'type' => 'transfer',
                'image' => 'https://images.unsplash.com/photo-1566371486490-560ded23b5e4?q=80&w=800',
                'is_active' => true,
                'features' => ['Private Vehicle', 'Water & Beer', 'Highway Tolls Included', 'Luxury Van']
            ],
            [
                'title' => 'Chichen Itza & Cenote Tour',
                'slug' => 'chichen-itza-cenote-tour',
                'description' => 'Guided tour to the Mayan pyramid of Kukulkan, followed by a refreshing swim in a sacred cenote.',
                'price' => 120.00,
                'price_usd' => 120.00,
                'price_mxn' => 2400.00,
                'duration_minutes' => 720,
                'type' => 'tour',
                'image' => 'https://images.unsplash.com/photo-1518638151313-982d2ba5011b?q=80&w=800',
                'is_active' => true,
                'features' => ['Roundtrip Transportation', 'Buffet Lunch', 'Certified Guide', 'Entrance Fees']
            ],
            [
                'title' => 'Luxury Catamaran to Isla Mujeres',
                'slug' => 'catamaran-isla-mujeres',
                'description' => 'Sail the Caribbean blue waters with open bar, snorkeling gear, and beach club access.',
                'price' => 95.00,
                'price_usd' => 95.00,
                'price_mxn' => 1900.00,
                'duration_minutes' => 420,
                'type' => 'tour',
                'image' => 'https://images.unsplash.com/photo-1544551763-46a42a46e865?q=80&w=800',
                'is_active' => true,
                'features' => ['Open Bar', 'Snorkeling Equipment', 'Spinnaker Activity', 'Beach Club']
            ],
            [
                'title' => 'Tulum Ruins & Turtle Snorkeling',
                'slug' => 'tulum-turtles',
                'description' => 'Visit the seaside ruins of Tulum and snorkel with turtles in Akumal Bay.',
                'price' => 110.00,
                'price_usd' => 110.00,
                'price_mxn' => 2200.00,
                'duration_minutes' => 480,
                'type' => 'tour',
                'image' => 'https://images.unsplash.com/photo-1506869640319-fe1a24fd76dc?q=80&w=800',
                'is_active' => true,
                'features' => ['Roundtrip Transportation', 'Box Lunch', 'Snorkel Gear', 'Guide']
            ],
            [
                'title' => 'Private Driver (Hourly)',
                'slug' => 'private-driver-hourly',
                'description' => 'Hire a private driver and vehicle for custom city tours, shopping, or dining.',
                'price' => 50.00,
                'price_usd' => 50.00,
                'price_mxn' => 1000.00,
                'duration_minutes' => 60,
                'type' => 'private',
                'image' => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?q=80&w=800',
                'is_active' => true,
                'features' => ['SUV or Van', 'Bilingual Driver', 'Custom Itinerary', 'Fuel Included']
            ],
        ];

        foreach ($services as $service) {
            $service['organization_id'] = $org->id;
            Service::updateOrCreate(['title' => $service['title']], $service);
        }

        // 4. Seed Blog Topics
        $topics = [
            ['name' => 'Travel Tips', 'slug' => 'travel-tips'],
            ['name' => 'Local Culture', 'slug' => 'local-culture'],
            ['name' => 'Destinations', 'slug' => 'destinations'],
            ['name' => 'Food & Drink', 'slug' => 'food-drink'],
        ];

        $createdTopics = [];
        foreach ($topics as $topic) {
            $createdTopics[$topic['slug']] = BlogTopic::firstOrCreate(['slug' => $topic['slug']], $topic);
        }

        // 5. Seed Blog Posts
        $posts = [
            [
                'title' => '10 Tips for a Perfect Cancun Vacation',
                'slug' => '10-tips-cancun-vacation',
                'content' => 'From packing essentials to the best time to visit, here is everything you need to know before you fly.',
                'excerpt' => 'From packing essentials to the best time to visit, here is everything you need to know before you fly.',
                'image' => 'https://images.unsplash.com/photo-1506929562872-bb421503ef21?q=80&w=800',
                'is_published' => true,
                'published_at' => now(),
                'user_id' => $user->id,
                'topic_id' => $createdTopics['travel-tips']->id,
                'read_time' => '5 min',
            ],
            [
                'title' => 'Why You Must Visit Chichen Itza',
                'slug' => 'why-visit-chichen-itza',
                'content' => 'Discover the history and mystery behind one of the New Seven Wonders of the World.',
                'excerpt' => 'Discover the history and mystery behind one of the New Seven Wonders of the World.',
                'image' => 'https://images.unsplash.com/photo-1518638151313-982d2ba5011b?q=80&w=800',
                'is_published' => true,
                'published_at' => now()->subDays(2),
                'user_id' => $user->id,
                'topic_id' => $createdTopics['destinations']->id,
                'read_time' => '7 min',
            ],
            [
                'title' => 'Top 5 Cenotes in Riviera Maya',
                'slug' => 'top-5-cenotes-riviera-maya',
                'content' => 'Explore the magical underground rivers and sinkholes distinct to the Yucatan Peninsula.',
                'excerpt' => 'Explore the magical underground rivers and sinkholes distinct to the Yucatan Peninsula.',
                'image' => 'https://images.unsplash.com/photo-1563782414411-b05969e9994c?q=80&w=800',
                'is_published' => true,
                'published_at' => now()->subDays(5),
                'user_id' => $user->id,
                'topic_id' => $createdTopics['destinations']->id,
                'read_time' => '6 min',
            ],
            [
                'title' => 'Best Tacos in Playa del Carmen',
                'slug' => 'best-tacos-playa-del-carmen',
                'content' => 'A foodie\s guide to the most authentic and delicious street tacos in Playa.',
                'excerpt' => 'A foodie\s guide to the most authentic and delicious street tacos in Playa.',
                'image' => 'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?q=80&w=800',
                'is_published' => true,
                'published_at' => now()->subDays(10),
                'user_id' => $user->id,
                'topic_id' => $createdTopics['food-drink']->id,
                'read_time' => '4 min',
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::create($post);
        }
    }
}
