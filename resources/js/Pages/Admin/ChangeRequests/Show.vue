<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    changeRequest: Object,
    currentData: Object,
});

const form = useForm({
    reason: ''
});

const approve = () => {
    if (confirm('Are you sure you want to approve and apply these changes?')) {
        form.post(route('admin.change-requests.approve', props.changeRequest.id));
    }
};

const reject = () => {
    const reason = prompt('Reason for rejection (optional):');
    if (reason !== null) {
        form.reason = reason;
        form.post(route('admin.change-requests.reject', props.changeRequest.id));
    }
};

// Helper to find changed keys
const changedKeys = Object.keys(props.changeRequest.payload);
</script>

<template>
    <Head title="Review Change Request" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Review Request #{{ changeRequest.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <div class="mb-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Proposed Changes</h3>
                            <p class="text-sm text-gray-500">By {{ changeRequest.user.name }} • {{ changeRequest.request_type }}</p>
                        </div>
                        <div class="space-x-3">
                            <button @click="reject" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                            <button @click="approve" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Approve & Apply</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Field</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Current Value</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Proposed Value</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="key in changedKeys" :key="key" class="hover:bg-yellow-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ key }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ currentData ? currentData[key] : 'N/A' }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-700">
                                        {{ changeRequest.payload[key] }}
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
