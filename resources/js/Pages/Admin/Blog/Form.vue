<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    post: Object,
    topics: Array,
});

const form = useForm({
    title: props.post?.title || '',
    topic_id: props.post?.topic_id || '',
    excerpt: props.post?.excerpt || '',
    content: props.post?.content || '',
    is_published: props.post?.is_published ?? false,
    image_file: null,
});

const submit = () => {
    if (props.post) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.blog.update', props.post.id));
    } else {
        form.post(route('admin.blog.store'));
    }
};
</script>

<template>
    <Head :title="post ? 'Edit Post' : 'Create Post'" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ post ? 'Edit Post' : 'Create New Post' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input v-model="form.title" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                            <div v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Topic</label>
                            <select v-model="form.topic_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                <option value="" disabled>Select a topic</option>
                                <option v-for="topic in topics" :key="topic.id" :value="topic.id">{{ topic.name }}</option>
                            </select>
                            <div v-if="form.errors.topic_id" class="text-red-500 text-xs mt-1">{{ form.errors.topic_id }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Excerpt (Summary)</label>
                            <textarea v-model="form.excerpt" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500"></textarea>
                            <div v-if="form.errors.excerpt" class="text-red-500 text-xs mt-1">{{ form.errors.excerpt }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Content</label>
                            <textarea v-model="form.content" rows="15" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 font-mono text-sm"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Accepts Markdown or HTML.</p>
                            <div v-if="form.errors.content" class="text-red-500 text-xs mt-1">{{ form.errors.content }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cover Image</label>
                            <div v-if="post?.image" class="mb-2">
                                <img :src="post.image" class="h-32 w-auto object-cover rounded">
                            </div>
                            <input type="file" @input="form.image_file = $event.target.files[0]" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                            <div v-if="form.errors.image_file" class="text-red-500 text-xs mt-1">{{ form.errors.image_file }}</div>
                        </div>

                        <div class="flex items-center">
                            <input v-model="form.is_published" type="checkbox" id="is_published" class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded">
                            <label for="is_published" class="ml-2 block text-sm text-gray-900">Publish immediately</label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing" class="bg-cyan-600 text-white px-6 py-2 rounded-md hover:bg-cyan-700 transition">
                                {{ post ? 'Update Post' : 'Create Post' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
