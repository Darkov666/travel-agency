<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    topics: Object,
});

const deleteTopic = (id) => {
    if (confirm('Are you sure you want to delete this topic?')) {
        router.delete(route('admin.blog-topics.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Blog Topics" />
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Blog Categories (Topics)
                </h2>
                <Link :href="route('admin.blog-topics.create')" class="px-4 py-2 bg-cyan-600 text-white rounded-md text-sm font-medium hover:bg-cyan-700 shadow flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    New Category
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul role="list" class="divide-y divide-gray-200">
                    <li v-for="topic in topics.data" :key="topic.id">
                        <div class="px-4 py-4 flex items-center sm:px-6">
                            <div class="min-w-0 flex-1 sm:flex sm:items-center sm:justify-between">
                                <div class="truncate">
                                    <div class="flex text-sm">
                                        <p class="font-medium text-cyan-600 truncate">{{ topic.name }}</p>
                                    </div>
                                    <div class="mt-2 flex">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                            </svg>
                                            <p>
                                                {{ topic.posts_count }} posts
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-5 flex-shrink-0 flex gap-2">
                                <Link :href="route('admin.blog-topics.edit', topic.id)" class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 text-sm">Edit</Link>
                                <button @click="deleteTopic(topic.id)" class="px-3 py-1 bg-red-400 text-white rounded hover:bg-red-500 text-sm">Delete</button>
                            </div>
                        </div>
                    </li>
                </ul>
                <!-- Simple Pagination if needed -->
                <div v-if="topics.links && topics.links.length > 3" class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between sm:px-6">
                     <div class="flex-1 flex justify-between sm:hidden">
                        <Link v-if="topics.prev_page_url" :href="topics.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"> Previous </Link>
                        <Link v-if="topics.next_page_url" :href="topics.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"> Next </Link>
                    </div>
                </div>
            </div>
            
             <div v-if="topics.data.length === 0" class="text-center py-10">
                <p class="text-gray-500">No categories found.</p>
            </div>
        </div>
    </AdminLayout>
</template>
