<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    reviews: Object,
    filters: Object,
});

const status = ref(props.filters.status || '');

const filterStatus = () => {
    router.get(
        route('admin.reviews.index'),
        { status: status.value },
        { preserveState: true }
    );
};

const approveReview = (review) => {
    router.post(route('admin.reviews.approve', review.id));
};

const deleteReview = (review) => {
    if (confirm('Are you sure you want to delete this review?')) {
        router.delete(route('admin.reviews.destroy', review.id));
    }
};
</script>

<template>
    <Head title="Reviews Management" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Customer Reviews</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Filters -->
                <div class="mb-6 flex gap-4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div>
                        <select v-model="status" @change="filterStatus" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Status</option>
                            <option value="pending">Pending Approval</option>
                            <option value="approved">Approved</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Reviewer</th>
                                    <th scope="col" class="px-6 py-3">Rating</th>
                                    <th scope="col" class="px-6 py-3">Content</th>
                                    <th scope="col" class="px-6 py-3">Date</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="review in reviews.data" :key="review.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ review.reviewer_name }}
                                        <div class="text-xs text-gray-500">Ref: {{ review.reservation ? review.reservation.booking_ref : 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex text-yellow-400">
                                            <span v-for="n in 5" :key="n">
                                                <span v-if="n <= review.rating">★</span>
                                                <span v-else class="text-gray-300">★</span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 italic">
                                        "{{ review.content }}"
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ new Date(review.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span v-if="review.is_approved" class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Approved</span>
                                        <span v-else class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Pending</span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button v-if="!review.is_approved" @click="approveReview(review)" class="font-medium text-green-600 dark:text-green-500 hover:underline">Approve</button>
                                        <button @click="deleteReview(review)" class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="reviews.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center">No reviews found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                     <!-- Simple Pagination -->
                    <div v-if="reviews.links && reviews.links.length > 3" class="p-4 flex gap-1 justify-center border-t dark:border-gray-700">
                        <Link 
                            v-for="(link, k) in reviews.links" 
                            :key="k"
                            :href="link.url || '#'"
                            v-html="link.label"
                            class="px-3 py-1 border rounded text-sm"
                            :class="{'bg-blue-600 text-white': link.active, 'opacity-50': !link.url}"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
