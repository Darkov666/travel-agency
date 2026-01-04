<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogTopic;
use Illuminate\Support\Str;

class BlogTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            'Travel Tips',
            'Destinations',
            'Company News',
            'Guides',
            'Culture & History',
        ];

        foreach ($topics as $name) {
            BlogTopic::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
