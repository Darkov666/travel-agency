<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue'; // Operators might use a simplified mobile-first layout
// Using MainLayout for now as it's responsive.

defineProps({
    today_tasks: Array,
    status: String
});
</script>

<template>
    <Head title="Operator Dashboard" />
    <MainLayout>
        <div class="py-6 bg-gray-100 min-h-screen">
            <div class="max-w-lg mx-auto px-4">
                
                <!-- Status Toggle -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6 text-center">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Current Status</h2>
                    <div class="inline-flex rounded-md shadow-sm" role="group">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-l-lg hover:bg-green-700">
                            Available
                        </button>
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100">
                            On Break
                        </button>
                        <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-r-lg hover:bg-red-700">
                            Off Duty
                        </button>
                    </div>
                     <p class="mt-2 text-sm text-gray-500">You are currently: <strong>{{ status }}</strong></p>
                </div>

                <!-- Today's Tasks -->
                <h3 class="text-xl font-bold text-gray-800 mb-4">Today's Services</h3>
                
                <div v-if="today_tasks.length === 0" class="text-center py-8 text-gray-500 bg-white rounded-lg shadow">
                    No services assigned for today.
                </div>

                <div v-for="task in today_tasks" :key="task.id" class="bg-white rounded-xl shadow-md p-6 mb-4 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start mb-2">
                        <span class="font-bold text-lg">#{{ task.reservation.booking_ref }}</span>
                        <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ task.time }}</span>
                    </div>
                    <div class="text-gray-700 mb-4">
                        <p class="font-medium">{{ task.service_name }}</p>
                        <div class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-map-marker-alt"></i> {{ task.zone_name }}
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                     <div class="grid grid-cols-2 gap-3">
                        <button class="bg-blue-600 text-white py-2 rounded font-bold text-sm">Start Trip</button>
                        <button class="bg-gray-200 text-gray-800 py-2 rounded font-bold text-sm">Details</button>
                     </div>
                </div>

            </div>
        </div>
    </MainLayout>
</template>
