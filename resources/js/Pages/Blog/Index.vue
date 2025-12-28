<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { debounce } from 'lodash';

const props = defineProps({
    posts: Object,
    topics: Array,
    filters: Object,
});

const searchQuery = ref(props.filters.search || '');
const selectedTopic = ref(props.filters.topic || 'all');

watch(searchQuery, debounce((value) => {
    router.get('/blog', { search: value, topic: selectedTopic.value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const selectTopic = (topicSlug) => {
    selectedTopic.value = topicSlug;
    router.get('/blog', { topic: topicSlug, search: searchQuery.value }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head title="Blog" />

    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-serif font-bold text-secondary-900 dark:text-white mb-4">Nuestro Blog</h1>
                    <p class="text-lg text-secondary-600 dark:text-secondary-400">Recursos, consejos y reflexiones para tu bienestar.</p>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-10 space-y-4 md:space-y-0">
                    <!-- Topics -->
                    <div class="flex space-x-2 overflow-x-auto pb-2 md:pb-0 w-full md:w-auto">
                        <button 
                            @click="selectTopic('all')"
                            :class="[
                                'px-4 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap',
                                selectedTopic === 'all'
                                    ? 'bg-primary-600 text-white shadow-md' 
                                    : 'bg-white dark:bg-secondary-800 text-secondary-600 dark:text-secondary-300 hover:bg-primary-50 dark:hover:bg-secondary-700'
                            ]"
                        >
                            Todos
                        </button>
                        <button 
                            v-for="topic in topics" 
                            :key="topic.id"
                            @click="selectTopic(topic.slug)"
                            :class="[
                                'px-4 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap',
                                selectedTopic === topic.slug
                                    ? 'bg-primary-600 text-white shadow-md' 
                                    : 'bg-white dark:bg-secondary-800 text-secondary-600 dark:text-secondary-300 hover:bg-primary-50 dark:hover:bg-secondary-700'
                            ]"
                        >
                            {{ topic.name }}
                        </button>
                    </div>

                    <!-- Search -->
                    <div class="relative w-full md:w-64">
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Buscar artículos..." 
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-secondary-300 dark:border-secondary-700 bg-white dark:bg-secondary-800 text-secondary-900 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Posts Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="post in posts.data" :key="post.id" class="bg-white dark:bg-secondary-800 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden group">
                        <!-- Image -->
                        <div class="relative h-48 overflow-hidden">
                            <!-- Placeholder image if no image provided, or use a default -->
                            <img :src="post.image || 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?q=80&w=800&auto=format&fit=crop'" :alt="post.title" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                            <div v-if="post.topic" class="absolute top-4 left-4 bg-white/90 dark:bg-black/70 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-primary-600 dark:text-primary-400 uppercase tracking-wide">
                                {{ post.topic.name }}
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="flex items-center text-xs text-secondary-500 dark:text-secondary-400 mb-3 space-x-2">
                                <span>{{ new Date(post.published_at).toLocaleDateString() }}</span>
                                <span>&bull;</span>
                                <span>{{ post.read_time || '5 min' }} de lectura</span>
                            </div>
                            <Link :href="`/blog/${post.slug}`" class="block">
                                <h3 class="text-xl font-serif font-bold text-secondary-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ post.title }}
                                </h3>
                            </Link>
                            <p class="text-secondary-600 dark:text-secondary-300 text-sm mb-4 line-clamp-3">
                                {{ post.excerpt }}
                            </p>
                            
                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-4 border-t border-secondary-100 dark:border-secondary-700">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-700 dark:text-primary-300 font-bold text-xs">
                                        {{ post.author ? post.author.name.charAt(0) : 'A' }}
                                    </div>
                                    <span class="text-xs font-medium text-secondary-700 dark:text-secondary-300">{{ post.author ? post.author.name : 'Admin' }}</span>
                                </div>
                                <!-- Likes/Comments (Optional: Add counts if available in list view) -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="posts.data.length === 0" class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-secondary-100 dark:bg-secondary-800 mb-4">
                        <svg class="h-8 w-8 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-secondary-900 dark:text-white mb-2">No se encontraron artículos</h3>
                    <p class="text-secondary-500 dark:text-secondary-400">Intenta ajustar tu búsqueda o filtros.</p>
                </div>

                <!-- Pagination -->
                <div v-if="posts.links.length > 3" class="mt-10 flex justify-center">
                    <div class="flex space-x-1">
                        <Link
                            v-for="(link, key) in posts.links"
                            :key="key"
                            :href="link.url"
                            v-html="link.label"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            :class="[
                                link.active
                                    ? 'bg-primary-600 text-white'
                                    : 'bg-white dark:bg-secondary-800 text-secondary-600 dark:text-secondary-300 hover:bg-gray-100 dark:hover:bg-secondary-700',
                                !link.url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
