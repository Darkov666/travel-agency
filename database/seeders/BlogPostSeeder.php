<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password')]
        );

        $topic = \App\Models\BlogTopic::create([
            'name' => 'General',
            'slug' => 'general',
        ]);

        \App\Models\BlogPost::create([
            'title' => 'Understanding Anxiety',
            'slug' => 'understanding-anxiety',
            'content' => 'Anxiety is a normal emotion. It’s your brain’s way of reacting to stress and alerting you of potential danger ahead...',
            'excerpt' => 'Anxiety is a normal emotion. It’s your brain’s way of reacting to stress...',
            'image' => 'https://images.unsplash.com/photo-1518609878373-06d740f60d8b?q=80&w=800&auto=format&fit=crop',
            'read_time' => '5 min',
            'is_published' => true,
            'published_at' => now(),
            'user_id' => $user->id,
            'topic_id' => $topic->id,
        ]);

        \App\Models\BlogPost::create([
            'title' => 'The Benefits of Mindfulness',
            'slug' => 'benefits-of-mindfulness',
            'content' => 'Mindfulness is the practice of purposely bringing one\'s attention in the present moment without judgment...',
            'excerpt' => 'Mindfulness is the practice of purposely bringing one\'s attention in the present moment...',
            'image' => 'https://images.unsplash.com/photo-1511295742362-92c96b50484f?q=80&w=800&auto=format&fit=crop',
            'read_time' => '4 min',
            'is_published' => true,
            'published_at' => now()->subDays(5),
            'user_id' => $user->id,
            'topic_id' => $topic->id,
        ]);
    }
}
