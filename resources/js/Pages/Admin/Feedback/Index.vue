<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    type: String, // 'website' or 'service'
    feedbacks: Object,
});

const currentType = ref(props.type);

watch(currentType, (val) => {
    router.get(route('admin.feedback.index'), { type: val }, { preserveState: true, replace: true });
});


// Actions for Service Reviews
const approveReview = (id) => router.post(route('admin.feedback.reviews.approve', id));
const rejectReview = (id) => router.post(route('admin.feedback.reviews.reject', id));
const deleteReview = (id) => {
    if(confirm('Delete this review?')) router.delete(route('admin.feedback.reviews.destroy', id));
}

// Actions for Website Feedback
const markReviewed = (id) => router.post(route('admin.feedback.website.reviewed', id));
const deleteFeedback = (id) => {
    if(confirm('Delete this feedback?')) router.delete(route('admin.feedback.website.destroy', id));
}

</script>

<template>
    <Head title="Feedback & Reviews" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Feedback & Reviews
            </h2>
        </template>

        <div class="py-6">
            <!-- Tabs -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button 
                            @click="currentType = 'website'"
                            :class="[currentType === 'website' ? 'border-cyan-500 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
                            Website Feedback (Internal)
                        </button>
                        <button 
                            @click="currentType = 'service'"
                            :class="[currentType === 'service' ? 'border-cyan-500 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
                            Service Reviews (Public)
                        </button>
                    </nav>
                </div>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul role="list" class="divide-y divide-gray-200" v-if="feedbacks.data.length > 0">
                        <li v-for="item in feedbacks.data" :key="item.id" class="px-4 py-4 sm:px-6 hover:bg-gray-50 transition">
                            <!-- Website Feedback Layout -->
                            <div v-if="currentType === 'website'" class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-cyan-600 truncate">
                                            User ID: {{ item.user_id || 'Anonymous' }}
                                        </p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <span v-if="item.is_reviewed" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Reviewed
                                            </span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                New
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center mb-1">
                                        <!-- Stars -->
                                        <div class="flex text-yellow-400">
                                            <span v-for="n in 5" :key="n" :class="n <= item.rating ? 'fas fa-star' : 'far fa-star'">★</span>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">{{ new Date(item.created_at).toLocaleDateString() }}</span>
                                    </div>

                                    <p class="mt-2 flex items-center text-sm text-gray-700">
                                        {{ item.comments || 'No comments provided.' }}
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0 flex flex-col gap-2">
                                     <button v-if="!item.is_reviewed" @click="markReviewed(item.id)" class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">Mark Read</button>
                                     <button @click="deleteFeedback(item.id)" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">Delete</button>
                                </div>
                            </div>

                            <!-- Service Review Layout -->
                            <div v-else class="flex items-center justify-between">
                                  <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-medium text-cyan-600 truncate">
                                            {{ item.reviewer_name }}
                                            <span class="text-gray-400 font-normal">on Service: {{ item.reservation?.service?.name || 'N/A' }}</span>
                                        </p>
                                        <div class="ml-2 flex-shrink-0 flex">
                                            <span v-if="item.is_approved" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Approved
                                            </span>
                                            <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Pending
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center mb-1">
                                         <div class="flex text-yellow-400">
                                            <span v-for="n in 5" :key="n" :class="n <= item.rating ? 'fas fa-star' : 'far fa-star'">★</span>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-500">{{ new Date(item.created_at).toLocaleDateString() }}</span>
                                    </div>

                                    <p class="mt-2 flex items-center text-sm text-gray-700 italic">
                                        "{{ item.content }}"
                                    </p>
                                </div>
                                <div class="ml-4 flex-shrink-0 flex flex-col gap-2">
                                     <button v-if="!item.is_approved" @click="approveReview(item.id)" class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200">Approve</button>
                                     <button v-if="item.is_approved" @click="rejectReview(item.id)" class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded hover:bg-yellow-200">Hide</button>
                                     <button @click="deleteReview(item.id)" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">Delete</button>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div v-else class="text-center py-10 text-gray-500">
                        No feedback found.
                    </div>
                     <!-- Pagination -->
                    <div v-if="feedbacks.links && feedbacks.links.length > 3" class="px-4 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link v-if="feedbacks.prev_page_url" :href="feedbacks.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"> Previous </Link>
                            <Link v-if="feedbacks.next_page_url" :href="feedbacks.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"> Next </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
