<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    post: Object,
    isLiked: Boolean,
    isSaved: Boolean,
});

const commentForm = useForm({
    content: '',
});

const submitComment = () => {
    commentForm.post(`/blog/${props.post.slug}/comment`, {
        preserveScroll: true,
        onSuccess: () => commentForm.reset(),
    });
};

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

const toggleCommentLike = (commentId) => {
    router.post(`/comments/${commentId}/like`, {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="post.title" />

    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen transition-colors duration-300">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="flex mb-8 text-sm text-secondary-500 dark:text-secondary-400">
                    <Link href="/" class="hover:text-primary-600 dark:hover:text-primary-400">Inicio</Link>
                    <span class="mx-2">/</span>
                    <Link href="/blog" class="hover:text-primary-600 dark:hover:text-primary-400">Blog</Link>
                    <span class="mx-2">/</span>
                    <span class="text-secondary-900 dark:text-white truncate">{{ post.title }}</span>
                </nav>

                <!-- Article Header -->
                <div class="mb-8">
                    <div v-if="post.topic" class="flex items-center space-x-2 text-sm text-primary-600 dark:text-primary-400 font-bold uppercase tracking-wide mb-3">
                        <span>{{ post.topic.name }}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-serif font-bold text-secondary-900 dark:text-white mb-4 leading-tight">
                        {{ post.title }}
                    </h1>
                    <div class="flex items-center justify-between border-b border-secondary-200 dark:border-secondary-700 pb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-700 dark:text-primary-300 font-bold">
                                {{ post.author ? post.author.name.charAt(0) : 'A' }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-secondary-900 dark:text-white">{{ post.author ? post.author.name : 'Admin' }}</p>
                                <p class="text-xs text-secondary-500 dark:text-secondary-400">{{ new Date(post.published_at).toLocaleDateString() }} &bull; {{ post.read_time || '5 min' }} de lectura</p>
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
                </div>

                <!-- Content -->
                <article class="prose prose-lg dark:prose-invert max-w-none mb-12 text-secondary-700 dark:text-secondary-300" v-html="post.content">
                </article>

                <!-- Comments Section -->
                <div class="bg-white dark:bg-secondary-800 rounded-2xl shadow-sm p-6 md:p-8">
                    <h3 class="text-2xl font-serif font-bold text-secondary-900 dark:text-white mb-6">Comentarios ({{ post.comments_count }})</h3>
                    
                    <!-- Comment Form -->
                    <div class="mb-8">
                        <textarea 
                            v-model="commentForm.content" 
                            rows="3" 
                            class="w-full rounded-lg border-secondary-300 dark:border-secondary-700 bg-gray-50 dark:bg-secondary-900 text-secondary-900 dark:text-white focus:ring-primary-500 focus:border-primary-500 mb-3"
                            placeholder="Deja un comentario..."
                        ></textarea>
                        <div class="flex justify-end">
                            <button 
                                @click="submitComment" 
                                class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="commentForm.processing || !commentForm.content.trim()"
                            >
                                Publicar Comentario
                            </button>
                        </div>
                    </div>

                    <!-- Comments List -->
                    <div class="space-y-6">
                        <div v-for="comment in post.comments" :key="comment.id" class="flex space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-secondary-200 dark:bg-secondary-700 flex items-center justify-center text-secondary-600 dark:text-secondary-300 font-bold">
                                    {{ comment.user ? comment.user.name.charAt(0) : 'U' }}
                                </div>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-sm font-bold text-secondary-900 dark:text-white">{{ comment.user ? comment.user.name : 'Usuario' }}</h4>
                                    <span class="text-xs text-secondary-500 dark:text-secondary-400">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                                </div>
                                <p class="text-secondary-700 dark:text-secondary-300 text-sm mb-2">{{ comment.content }}</p>
                                <div class="flex items-center space-x-4">
                                    <button @click="toggleCommentLike(comment.id)" class="flex items-center space-x-1 text-xs text-secondary-500 hover:text-primary-600 transition-colors" :class="{'text-primary-600': comment.isLiked}">
                                        <svg class="h-4 w-4" :fill="comment.isLiked ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                        </svg>
                                        <span>{{ comment.likes_count }}</span>
                                    </button>
                                    <button class="text-xs text-secondary-500 hover:text-primary-600 transition-colors">Responder</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
