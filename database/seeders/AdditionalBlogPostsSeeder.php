<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogPost;
use App\Models\BlogTopic;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdditionalBlogPostsSeeder extends Seeder
{
    public function run()
    {
        // Ensure we have a user
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Psic. Ana García',
                'email' => 'ana@example.com',
            ]);
        }

        // Ensure we have topics
        $topics = [
            ['name' => 'Estrés', 'slug' => 'estres'],
            ['name' => 'Sueño', 'slug' => 'sueno'],
            ['name' => 'Crecimiento Personal', 'slug' => 'crecimiento-personal'],
        ];

        foreach ($topics as $topicData) {
            BlogTopic::firstOrCreate(['slug' => $topicData['slug']], $topicData);
        }

        $posts = [
            [
                'title' => 'El impacto del estrés en tu salud física',
                'slug' => 'impacto-estres-salud-fisica',
                'content' => '<p>El estrés no solo afecta tu mente, también tiene un impacto profundo en tu cuerpo. Desde dolores de cabeza hasta problemas digestivos, aprender a gestionar el estrés es vital para tu salud integral.</p><p>Practicar técnicas de relajación y ejercicio regular puede marcar una gran diferencia.</p>',
                'topic_slug' => 'estres',
                'image' => 'https://images.unsplash.com/photo-1518609878373-06d740f60d8b?q=80&w=800&auto=format&fit=crop',
            ],
            [
                'title' => 'Higiene del sueño: Claves para descansar mejor',
                'slug' => 'higiene-sueno-descansar-mejor',
                'content' => '<p>Dormir bien es fundamental para la recuperación cognitiva y emocional. Establecer una rutina nocturna, evitar pantallas antes de dormir y mantener un ambiente fresco son pasos sencillos para mejorar tu calidad de sueño.</p>',
                'topic_slug' => 'sueno',
                'image' => 'https://images.unsplash.com/photo-1511295742362-92c96b50484f?q=80&w=800&auto=format&fit=crop',
            ],
            [
                'title' => 'Estableciendo límites saludables',
                'slug' => 'estableciendo-limites-saludables',
                'content' => '<p>Aprender a decir "no" es una de las habilidades más importantes para el autocuidado. Los límites saludables te permiten proteger tu energía y mantener relaciones más equilibradas y respetuosas.</p>',
                'topic_slug' => 'crecimiento-personal',
                'image' => 'https://images.unsplash.com/photo-1499209974431-9dddcece7f88?q=80&w=800&auto=format&fit=crop',
            ],
        ];

        foreach ($posts as $index => $postData) {
            $topic = BlogTopic::where('slug', $postData['topic_slug'])->first();

            BlogPost::create([
                'title' => $postData['title'],
                'slug' => $postData['slug'],
                'content' => $postData['content'],
                'excerpt' => Str::limit(strip_tags($postData['content']), 150),
                'image' => $postData['image'],
                'is_published' => true,
                'published_at' => Carbon::now()->addMinutes($index), // Ensure they are the most recent
                'user_id' => $user->id,
                'topic_id' => $topic->id,
                'read_time' => rand(3, 8) . ' min',
            ]);
        }
    }
}
