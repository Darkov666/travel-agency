<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    providerServices: Array
});

const form = useForm({
    client_name: '',
    client_email: '',
    client_phone: '',
    provider_service_id: '',
    date: '',
    time: '12:00',
    pax: 1,
    pickup_location: ''
});

const submit = () => {
    form.post(route('admin.reservations.store'));
};
</script>

<template>
    <Head title="Create Reservation" />
    <AdminLayout>
        <template #header>
            Create Manual Reservation
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Client Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Client Name</label>
                                <input v-model="form.client_name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                <span v-if="form.errors.client_name" class="text-red-500 text-xs">{{ form.errors.client_name }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input v-model="form.client_email" type="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                <span v-if="form.errors.client_email" class="text-red-500 text-xs">{{ form.errors.client_email }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <input v-model="form.client_phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Service Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Service / Product</label>
                                <select v-model="form.provider_service_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                    <option value="" disabled>Select a service...</option>
                                    <option v-for="svc in providerServices" :key="svc.id" :value="svc.id">
                                        {{ svc.label }}
                                    </option>
                                </select>
                                <span v-if="form.errors.provider_service_id" class="text-red-500 text-xs">{{ form.errors.provider_service_id }}</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date</label>
                                    <input v-model="form.date" type="date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                    <span v-if="form.errors.date" class="text-red-500 text-xs">{{ form.errors.date }}</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Time</label>
                                    <input v-model="form.time" type="time" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                    <span v-if="form.errors.time" class="text-red-500 text-xs">{{ form.errors.time }}</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Passengers (Pax)</label>
                                    <input v-model="form.pax" type="number" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                    <span v-if="form.errors.pax" class="text-red-500 text-xs">{{ form.errors.pax }}</span>
                                </div>
                                 <div>
                                    <label class="block text-sm font-medium text-gray-700">Pickup Location / Detail</label>
                                    <input v-model="form.pickup_location" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <Link :href="route('admin.reservations.index')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">Cancel</Link>
                        <button type="submit" :disabled="form.processing" class="bg-cyan-600 text-white px-4 py-2 rounded-md hover:bg-cyan-700">
                            Create Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
