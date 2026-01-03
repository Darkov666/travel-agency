<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    organization: Object,
    stats: Object,
    chartData: Array
});
</script>

<template>
    <Head title="Admin Dashboard" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ organization?.name || 'Organization' }} Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Stripe Connect Alert -->
                <div v-if="organization && !organization.stripe_connect_id" class="bg-red-50 border-l-4 border-red-400 p-4 mb-8 flex justify-between items-center shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <!-- Warning Icon -->
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Payment Setup Required
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>
                                    To receive payouts and accept payments from clients, you must connect your Stripe account. 
                                    Your account is currently limited.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a :href="route('admin.stripe.connect')" class="whitespace-nowrap inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Connect Stripe
                        </a>
                    </div>
                </div>

                <div v-else class="bg-green-50 border-l-4 border-green-400 p-4 mb-8 flex items-center shadow-sm">
                     <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            Stripe Connected • Payouts Enabled
                        </p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-400">
                        <div class="text-gray-500 text-sm">Pending Assignments</div>
                        <div class="text-2xl font-bold">{{ stats.pending }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-400">
                        <div class="text-gray-500 text-sm">Scheduled Today</div>
                        <div class="text-2xl font-bold">{{ stats.today }}</div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-400">
                        <div class="text-gray-500 text-sm">Completed (Month)</div>
                        <div class="text-2xl font-bold">{{ stats.completed_month }}</div>
                    </div>
                     <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-400">
                        <div class="text-gray-500 text-sm">Issues / Alerts</div>
                        <div class="text-2xl font-bold">{{ stats.issues }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Upcoming Reservations (14 Days)</h3>
                    
                    <div class="relative h-64 flex items-end justify-between space-x-2">
                        <!-- Y-Axis Grid Lines (Simplified) -->
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none text-xs text-gray-400">
                           <div class="border-b border-gray-100 w-full h-0"></div>
                           <div class="border-b border-gray-100 w-full h-0"></div>
                           <div class="border-b border-gray-100 w-full h-0"></div>
                           <div class="border-b border-gray-100 w-full h-0"></div>
                        </div>

                        <div v-for="day in chartData" :key="day.date" class="relative flex flex-col items-center flex-1 group">
                             <!-- Tooltip -->
                             <div class="absolute -top-10 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-800 text-white text-xs rounded py-1 px-2 z-10 whitespace-nowrap mb-2 pointer-events-none">
                                {{ day.count }} reservations<br>{{ day.date }}
                                <div class="absolute bottom-0 left-1/2 -ml-1 -mb-1 w-2 h-2 bg-gray-800 transform rotate-45"></div>
                            </div>
                            
                            <!-- Bar -->
                            <div 
                                class="w-full bg-cyan-500 rounded-t hover:bg-cyan-600 transition-all duration-300 relative z-0"
                                :style="{ height: `${Math.max(day.count * 10, 4)}%` }" 
                            > <!-- Identifying max height dynamically would be better, but assuming low volume for now -->
                            </div>
                            
                            <!-- Label -->
                            <div class="mt-2 text-xs text-center text-gray-500 font-medium">
                                <span class="hidden md:block">{{ day.day }}</span>
                                <span class="md:hidden">{{ day.day.charAt(0) }}</span>
                                <div class="text-[10px] text-gray-400">{{ day.date }}</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
