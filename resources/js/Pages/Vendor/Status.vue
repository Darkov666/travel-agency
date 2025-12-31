<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    status: String,
    item: Object
});
</script>

<template>
    <Head title="Provider Confirmation" />
    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
                    
                    <div v-if="status === 'success'">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                            <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Service Accepted!</h1>
                        <p class="text-gray-600 dark:text-gray-300">
                            You have successfully accepted the service for Booking <strong>#{{ item.reservation.booking_ref }}</strong>.
                        </p>
                        <p class="text-gray-500 mt-4 text-sm">
                            Please check your email for the Work Order details.
                        </p>
                    </div>

                    <div v-if="status === 'already_accepted'">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
                            <svg class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Already Accepted</h1>
                        <p class="text-gray-600 dark:text-gray-300">
                            This service has already been accepted.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </MainLayout>
</template>
