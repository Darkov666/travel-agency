<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    service: Object
});

const form = useForm({
    price_public: props.service.price_public,
    cost_net: props.service.cost_net,
    max_pax: props.service.max_pax,
});

const submit = () => {
    form.put(route('provider.services.update', props.service.id));
};
</script>

<template>
    <Head title="Edit Service" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Service: {{ service.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Read Only Info -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Zone</label>
                                <div class="mt-1 p-2 bg-gray-100 rounded">{{ service.zone?.name }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Type</label>
                                <div class="mt-1 p-2 bg-gray-100 rounded">{{ service.type }}</div>
                            </div>
                        </div>

                        <!-- Editable Fields -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Net Cost (Your Rate)</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input v-model="form.cost_net" type="number" step="0.01" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">The amount you receive.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Public Price (Client Pays)</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input v-model="form.price_public" type="number" step="0.01" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div>
                             <label class="block text-sm font-medium text-gray-700">Max Passengers</label>
                             <input v-model="form.max_pax" type="number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="flex items-center justify-end border-t pt-4">
                            <Link :href="route('provider.services.index')" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</Link>
                            <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none disabled:opacity-50">
                                {{ form.processing ? 'Submitting...' : 'Request Changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
