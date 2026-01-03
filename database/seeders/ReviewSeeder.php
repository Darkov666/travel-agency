<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\Review;
use App\Models\BlogPost;
use App\Models\BlogTopic;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Str;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        // Ensure we have an organization
        $org = Organization::first();
        if (!$org) {
            $org = Organization::create([
                'name' => 'Default Org',
                'slug' => 'default',
                'is_active' => true,
                'hosting_mode' => 'subdomain',
            ]);
        }

        // Ensure we have a user
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'organization_id' => $org->id,
            ]);
        }

        // Ensure we have some blog topics
        $topic = BlogTopic::firstOrCreate(['slug' => 'travel-tips'], ['name' => 'Travel Tips']);

        // Create Blog Posts if none exist
        if (BlogPost::count() == 0) {
            BlogPost::create([
                'title' => 'Top 10 Cenotes in Riviera Maya',
                'slug' => 'top-10-cenotes',
                'content' => 'Full guide about Cenotes...',
                'excerpt' => 'Discover the hidden gems of the jungle.',
                'image' => 'https://placehold.co/800x600/2AC1D8/FFFFFF/png?text=Cenotes',
                'is_published' => true,
                'published_at' => now(),
                'user_id' => $user->id,
                'topic_id' => $topic->id,
            ]);

            BlogPost::create([
                'title' => 'How to prepare for your Chichen Itza Tour',
                'slug' => 'prepare-chichen-itza',
                'content' => 'Bring water, hat, and comfortable shoes...',
                'excerpt' => 'Essential tips for a perfect day at the ruins.',
                'image' => 'https://placehold.co/800x600/FF7F50/FFFFFF/png?text=Chichen+Itza',
                'is_published' => true,
                'published_at' => now()->subDay(),
                'user_id' => $user->id,
                'topic_id' => $topic->id,
            ]);

            BlogPost::create([
                'title' => 'Best Beaches in Tulum',
                'slug' => 'best-beaches-tulum',
                'content' => 'Paraiso Beach is a must visit...',
                'excerpt' => 'White sand, crystal clear water, and palm trees.',
                'image' => 'https://placehold.co/800x600/F4A460/FFFFFF/png?text=Tulum+Beach',
                'is_published' => true,
                'published_at' => now()->subDays(2),
                'user_id' => $user->id,
                'topic_id' => $topic->id,
            ]);
        }

        // Create Reviews for existing reservations or mock ones
        // Force create reservations to ensure no missing dependencies
        $reservation = Reservation::create([
            'booking_ref' => 'RES-' . strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'contact_name' => 'Jane Doe',
            'contact_email' => 'jane@example.com',
            'total_amount' => 100.00,
            'status' => 'completed',
            'organization_id' => $org->id,
        ]);

        Review::create([
            'reservation_id' => $reservation->id,
            'token' => Str::random(32),
            'rating' => 5,
            'content' => 'Absolutely fantastic service! The driver was on time and very polite.',
            'reviewer_name' => 'John Doe',
            'is_approved' => true,
        ]);

        // Create a second dummy reservation for another review
        $res2 = Reservation::create([
            'booking_ref' => 'RES-' . strtoupper(Str::random(8)),
            'user_id' => $user->id,
            'contact_name' => 'Mike Smith',
            'contact_email' => 'mike@example.com',
            'total_amount' => 150.00,
            'status' => 'completed',
            'organization_id' => $org->id,
        ]);

        Review::create([
            'reservation_id' => $res2->id,
            'token' => Str::random(32),
            'rating' => 4,
            'content' => 'Good trip, slight delay but comfortable.',
            'reviewer_name' => 'Mike Smith',
            'is_approved' => true,
        ]);

        $this->command->info('Reviews and Blog Posts seeded successfully.');
    }
}
