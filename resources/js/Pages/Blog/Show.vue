<script setup>
import { router } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import CommentSection from '@/Components/CommentSection.vue';

const props = defineProps({
    post: Object,
    isLiked: Boolean,
    isSaved: Boolean,
});

const toggleLike = () => {
    router.post(`/blog/${props.post.slug}/like`, {}, {
        preserveScroll: true,
    });
};

const toggleSave = () => {
    router.post(`/blog/${props.post.slug}/save`, {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="post.title" />

    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen transition-colors duration-300">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Entire Post Container -->
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-12">
                    <!-- Breadcrumb -->
                    <nav class="flex mb-8 text-sm text-gray-500">
                        <Link href="/" class="hover:text-primary-600">Inicio</Link>
                        <span class="mx-2">/</span>
                        <Link href="/blog" class="hover:text-primary-600">Blog</Link>
                        <span class="mx-2">/</span>
                        <span class="text-black truncate">{{ post.title }}</span>
                    </nav>

                    <!-- Article Header -->
                    <div class="mb-8">
                        <div v-if="post.topic" class="flex items-center space-x-2 text-sm text-primary-600 font-bold uppercase tracking-wide mb-3">
                            <span>{{ post.topic.name }}</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-serif font-bold text-black mb-4 leading-tight">
                            {{ post.title }}
                        </h1>
                        <div class="flex items-center justify-between border-b border-gray-200 pb-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold">
                                    {{ post.author ? post.author.name.charAt(0) : 'A' }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-black">{{ post.author ? post.author.name : 'Admin' }}</p>
                                    <p class="text-xs text-gray-500">{{ new Date(post.published_at).toLocaleDateString() }} &bull; {{ post.read_time || '5 min' }} de lectura</p>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                        <div class="flex items-center space-x-4">
                            <button @click="toggleLike" class="flex items-center space-x-1 text-secondary-500 hover:text-red-500 transition-colors" :class="{'text-red-500': isLiked}">
                                <svg class="h-6 w-6" :fill="isLiked ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span class="hidden sm:inline">{{ post.likes_count }}</span>
                            </button>
                            <button @click="toggleSave" class="text-secondary-500 hover:text-primary-600 transition-colors" :class="{'text-primary-600': isSaved}">
                                <svg class="h-6 w-6" :fill="isSaved ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="mb-10 rounded-2xl overflow-hidden shadow-lg">
                    <img :src="post.image || 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?q=80&w=800&auto=format&fit=crop'" :alt="post.title" class="w-full h-auto object-cover">
                    <!-- Content -->
                    <article class="prose prose-lg max-w-none text-black p-6" v-html="post.content">
                    </article>
                </div>
                </div>

                <!-- Comments Section -->
                <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8">
                    <CommentSection 
                        :comments="post.comments" 
                        :commentable-id="post.id" 
                        commentable-type="App\Models\BlogPost" 
                    />
                </div>
            </div>
        </div>
    </MainLayout>
</template>
