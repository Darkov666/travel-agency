<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    items: Object // Paginator
});

const form = useForm({
    provider_id: ''
});

const assign = (item, providerId) => {
    if (!confirm('Assign this provider? This will send an email.')) return;
    form.provider_id = providerId;
    form.post(route('admin.reservations.assign', item.id));
};

const cancel = (item) => {
    if (!confirm('Cancel this assignment? This will notify the provider.')) return;
    form.post(route('admin.reservations.cancel_vendor', item.id));
};
</script>

<template>
    <Head title="Admin - Reservations" />
    
    <AdminLayout>
        <template #header>
            Reservation Operations
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Reservation Items (Service Control)</h3>
                        <Link :href="route('admin.reservations.create')" class="bg-cyan-600 text-white px-4 py-2 rounded shadow text-sm hover:bg-cyan-700">
                            + New Reservation
                        </Link>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in items.data" :key="item.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ item.reservation.booking_ref }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ item.service_name }} ({{ item.zone_name }})
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ item.date }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': item.vendor_status === 'pending',
                                                'bg-green-100 text-green-800': item.vendor_status === 'accepted',
                                                'bg-red-100 text-red-800': item.vendor_status === 'rejected' || item.vendor_status === 'cancelled',
                                            }">
                                            {{ item.vendor_status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ item.assigned_provider?.name || 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <div v-if="!item.assigned_provider_id">
                                            <button 
                                                v-if="$page.props.auth.user.role === 'root' || $page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'supervisor'"
                                                @click="assign(item, item.provider_service?.provider_id)" 
                                                class="text-teal-600 hover:text-teal-900 font-bold"
                                            >
                                                {{ item.provider_service?.provider_id ? 'Assign Default' : 'Assign Provider' }}
                                            </button>
                                        </div>
                                        <div v-else class="flex flex-col space-y-1">
                                             <button @click="assign(item, null)" class="text-indigo-600 hover:text-indigo-900 text-xs">Reassign</button>
                                             <button @click="cancel(item)" class="text-red-600 hover:text-red-900 text-xs">Cancel Vendor</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
