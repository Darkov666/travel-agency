<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
    comments: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

watch(search, debounce((value) => {
    router.get(route('admin.comments.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

const approve = (id) => {
    if (confirm('Approve this comment?')) {
        router.post(route('admin.comments.approve', id));
    }
};

const reject = (id) => {
    if (confirm('Reject/Unpublish this comment?')) {
        router.post(route('admin.comments.reject', id));
    }
};

const deleteComment = (id) => {
    if (confirm('Permanently delete this comment?')) {
        router.delete(route('admin.comments.destroy', id));
    }
};
</script>

<template>
    <Head title="Comments Moderation" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Comments Moderation</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between mb-4">
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Search comments..." 
                                class="border-gray-300 rounded-md shadow-sm"
                            >
                            <div class="space-x-2">
                                <Link :href="route('admin.comments.index', { status: 'pending' })" class="text-blue-600 hover:underline">Pending</Link>
                                <Link :href="route('admin.comments.index', { status: 'approved' })" class="text-green-600 hover:underline">Approved</Link>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="comment in comments.data" :key="comment.id" 
                                    @click="router.visit(route('admin.comments.show', comment.id))"
                                    class="hover:bg-gray-50 cursor-pointer transition-colors"
                                >
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ comment.user ? comment.user.name : (comment.guest_name ? comment.guest_name + ' (Guest)' : 'Unknown') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ comment.user ? comment.user.email : comment.guest_email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-900 line-clamp-3">{{ comment.content }}</p>
                                        <p class="text-xs text-gray-500 mt-1">On: {{ comment.commentable_type.split('\\').pop() }} #{{ comment.commentable_id }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="comment.is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                            {{ comment.is_approved ? 'Approved' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ new Date(comment.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click.stop="approve(comment.id)" 
                                                class="text-green-600 hover:text-green-900 mr-3 disabled:opacity-50"
                                                :disabled="comment.is_approved">
                                            Approve
                                        </button>
                                        
                                        <button @click.stop="reject(comment.id)" 
                                                class="text-yellow-600 hover:text-yellow-900 mr-3 disabled:opacity-50"
                                                :disabled="!comment.is_approved">
                                            Reject
                                        </button>

                                        <button @click.stop="deleteComment(comment.id)" 
                                                class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        <div class="mt-4" v-if="comments.links">
                            <!-- Simple Previous/Next for brevity, or full pagination component -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
```
