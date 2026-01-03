<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    logs: Object // Paginator
});

const getActionColor = (action) => {
    if (action.includes('login') || action.includes('logout')) return 'text-blue-600 bg-blue-50';
    if (action.includes('create') || action.includes('confirm')) return 'text-green-600 bg-green-50';
    if (action.includes('delete') || action.includes('reject')) return 'text-red-600 bg-red-50';
    return 'text-gray-600 bg-gray-50';
};
</script>

<template>
    <Head title="Activity Logs" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Audit Trail / Activity Logs
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="log in logs.data" :key="log.id" class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ log.created_at }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ log.user }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs font-bold uppercase" :class="getActionColor(log.action)">
                                                {{ log.action }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 text-wrap">{{ log.description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">{{ log.ip }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex justify-end" v-if="logs.links">
                            <template v-for="(link, key) in logs.links" :key="key">
                                <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                                <Link v-else class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-blue-700 text-white': link.active }" :href="link.url" v-html="link.label" />
                            </template>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
