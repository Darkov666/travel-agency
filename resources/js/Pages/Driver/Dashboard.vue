<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'; // Using AppLayout for mobile-friendliness or AdminLayout? Driver Portal usually mobile.
// Assuming we use AdminLayout for consistency, or maybe a simpler layout.
// User didn't specify, but "type uber" implies mobile.
// Let's use a simple layout or reuse AdminLayout but clean.
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Badge from '@/Components/Badge.vue';
import { format } from 'date-fns';

defineProps({
    orders: Object,
    tab: String,
});

const statusColors = {
    pending: 'yellow',
    assigned: 'blue',
    accepted: 'green',
    rejected: 'red',
    en_route_base: 'indigo',
    at_pickup: 'purple',
    on_board: 'pink',
    finished: 'gray',
    cancelled: 'red',
};
</script>

<template>
    <AdminLayout label="My Assignments">
        <Head title="Driver Dashboard" />

        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-6">
                <Link :href="route('driver.dashboard', { tab: 'active' })"
                      class="flex-1 text-center py-2 px-4 border-b-2 font-medium text-sm"
                      :class="tab !== 'history' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                    Active
                </Link>
                <Link :href="route('driver.dashboard', { tab: 'history' })"
                      class="flex-1 text-center py-2 px-4 border-b-2 font-medium text-sm"
                      :class="tab === 'history' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                    History
                </Link>
            </div>

            <!-- List -->
            <div class="space-y-4">
                <div v-for="order in orders.data" :key="order.id" class="bg-white overflow-hidden shadow rounded-lg">
                    <Link :href="route('driver.order.show', order.id)" class="block px-4 py-5 sm:p-6 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="text-sm font-medium text-indigo-600 truncate">
                                #{{ order.folio }}
                            </div>
                            <div class="ml-2 flex-shrink-0 flex">
                                <Badge :color="statusColors[order.status]">{{ order.status }}</Badge>
                            </div>
                        </div>
                        <div class="mt-2 text-base font-semibold text-gray-900">
                             {{ order.reservation_item?.service_name }}
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ order.reservation_item?.date ? format(new Date(order.reservation_item.date), 'MMM dd, yyyy') : 'N/A' }}
                                </p>
                                <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ order.reservation_item?.pickup_time || 'TBA' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-500">
                            {{ order.reservation_item?.pickup_location }} → {{ order.reservation_item?.dropoff_location }}
                        </div>
                    </Link>
                </div>

                <div v-if="orders.data.length === 0" class="text-center py-10 bg-white rounded-lg shadow">
                    <p class="text-gray-500">No {{ tab }} orders found.</p>
                </div>
            </div>
            
            <!-- Simple Pagination -->
             <div class="mt-4" v-if="orders.links.length > 3">
                <Link v-for="(link, key) in orders.links" :key="key" 
                    :href="link.url ?? '#'" 
                    v-html="link.label"
                    class="mr-1 mb-1 px-3 py-2 text-sm border rounded inline-block"
                    :class="{ 'bg-indigo-600 text-white': link.active, 'text-gray-400': !link.url }"
                />
             </div>
        </div>
    </AdminLayout>
</template>
