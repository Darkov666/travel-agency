<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\User;
use Faker\Factory as Faker;

class InteractionsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $posts = BlogPost::all();
        $users = User::all();

        foreach ($posts as $post) {
            // Add some comments
            for ($i = 0; $i < rand(3, 8); $i++) {
                $isGuest = rand(0, 1);
                $isApproved = rand(0, 1);

                // If guest, user_id is null. If user, pick a random user.
                $userId = $isGuest ? null : ($users->count() > 0 ? $users->random()->id : null);

                $comment = Comment::create([
                    'content' => $faker->paragraph,
                    'user_id' => $userId,
                    'blog_post_id' => $post->id,
                    'is_approved' => $isApproved, // Mix of approved and unapproved
                    'ip_address' => $faker->ipv4,
                ]);

                // Add likes to comments
                for ($j = 0; $j < rand(0, 5); $j++) {
                    $comment->likes()->create([
                        'user_id' => rand(0, 1) ? ($users->count() > 0 ? $users->random()->id : null) : null,
                        'ip_address' => $faker->ipv4,
                    ]);
                }
            }

            // Add likes to post
            for ($k = 0; $k < rand(5, 20); $k++) {
                $post->likes()->create([
                    'user_id' => rand(0, 1) ? ($users->count() > 0 ? $users->random()->id : null) : null,
                    'ip_address' => $faker->ipv4,
                ]);
            }
        }
    }
}
