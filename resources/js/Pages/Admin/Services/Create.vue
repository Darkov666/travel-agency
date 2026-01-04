<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const form = useForm({
    title: '',
    type: 'transfer', // Default relevant to Agency
    description: '',
    net_price: 0,
    commission: 0,
    commission_type: 'fixed',
    price: 0,
    is_active: true,
});

// Auto-calculate Public Price
watch([() => form.net_price, () => form.commission, () => form.commission_type], () => {
    const net = parseFloat(form.net_price) || 0;
    const comm = parseFloat(form.commission) || 0;
    
    if (form.commission_type === 'fixed') {
        form.price = (net + comm).toFixed(2);
    } else {
        form.price = (net * (1 + comm / 100)).toFixed(2);
    }
});

const submit = () => {
    form.post(route('admin.services.store'));
};
</script>

<template>
    <AdminLayout title="Create Service">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Service</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Service Name</label>
                                <input v-model="form.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                             <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700">Type</label>
                                <select v-model="form.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="transfer">Transfer</option>
                                    <option value="tour">Tour</option>
                                    <option value="water">Water Activity</option>
                                    <option value="special">Special</option>
                                </select>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Description (Learn More Content)</label>
                                <textarea v-model="form.description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>

                            <!-- Pricing -->
                            <div class="col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Pricing</h3>
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700">Net Price (MXN)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input v-model="form.net_price" type="number" step="0.01" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00">
                                </div>
                            </div>

                            <div class="flex space-x-4">
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700">Commission Type</label>
                                    <select v-model="form.commission_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="fixed">Fixed Amount</option>
                                        <option value="percentage">Percentage (%)</option>
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700">Commission</label>
                                    <input v-model="form.commission" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700">Public Price (Calculated)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input v-model="form.price" type="number" step="0.01" class="bg-gray-100 focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" readonly>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Auto-calculated based on Net Price + Commission.</p>
                            </div>

                            <div class="col-span-2">
                                <div class="flex items-center">
                                    <input v-model="form.is_active" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label class="ml-2 block text-sm text-gray-900">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <Link :href="route('admin.services.index')" class="mr-4 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancel</Link>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700" :disabled="form.processing">
                                Create Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
