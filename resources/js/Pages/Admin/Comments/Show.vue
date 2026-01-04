<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    comment: Object,
});

const approve = () => {
    if (confirm('Are you sure you want to approve this comment?')) {
        router.post(route('admin.comments.approve', props.comment.id));
    }
};

const reject = () => {
    if (confirm('Are you sure you want to hide this comment?')) {
        router.post(route('admin.comments.reject', props.comment.id));
    }
};

const deleteComment = () => {
    if (confirm('Are you sure you want to permanently delete this comment?')) {
        router.delete(route('admin.comments.destroy', props.comment.id));
    }
};
</script>

<template>
    <Head title="Comment Details" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Comment #{{ comment.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-6">
                        
                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 mb-6">
                            <PrimaryButton @click="approve" :disabled="comment.is_approved" class="bg-green-600 hover:bg-green-700 disabled:opacity-50">
                                Approve
                            </PrimaryButton>
                            
                            <PrimaryButton @click="reject" :disabled="!comment.is_approved" class="bg-yellow-600 hover:bg-yellow-700 border-yellow-600 disabled:opacity-50">
                                Reject (Hide)
                            </PrimaryButton>

                            <DangerButton @click="deleteComment">
                                Delete (Permanent)
                            </DangerButton>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                                <p class="mt-1 text-sm font-bold" 
                                   :class="comment.is_approved ? 'text-green-600' : 'text-yellow-600'">
                                    {{ comment.is_approved ? 'Approved' : 'Pending' }}
                                </p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                                <p class="mt-1 text-sm text-gray-900">{{ new Date(comment.created_at).toLocaleString() }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Author</h3>
                                <div class="mt-1">
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ comment.user ? comment.user.name : (comment.guest_name || 'Guest') }}
                                        <span v-if="comment.user" class="text-xs text-blue-500 border border-blue-500 rounded px-1 ml-1">Reg</span>
                                        <span v-else class="text-xs text-gray-500 border border-gray-500 rounded px-1 ml-1">Guest</span>
                                    </p>
                                    <p class="text-sm text-gray-600">{{ comment.user ? comment.user.email : comment.guest_email }}</p>
                                    <p class="text-xs text-gray-400 mt-1">IP: {{ comment.ip_address }}</p>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Context</h3>
                                <p class="mt-1 text-sm text-gray-900">
                                    On <strong>{{ comment.commentable_type.split('\\').pop() }}</strong> #{{ comment.commentable_id }}
                                </p>
                                <!-- Can add link to actual post if needed -->
                            </div>
                        </div>

                        <hr class="border-gray-200">

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Content</h3>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 text-gray-800 whitespace-pre-wrap leading-relaxed">
                                {{ comment.content }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
