<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    providers: Array,
});

const deleteProvider = (provider) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.providers.destroy', provider.id));
        }
    });
};
</script>

<template>
    <AdminLayout>
        <Head title="Providers" />

        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Providers</h2>
            <Link :href="route('admin.providers.create')" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500">
                Add Provider
            </Link>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                <li v-for="provider in providers" :key="provider.id" class="p-4 hover:bg-gray-50 dark:hover:bg-gray-750 transition cursor-pointer" @click="router.visit(route('admin.providers.edit', provider.id))">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0 gap-x-4">
                            <div v-if="provider.logo_path" class="h-12 w-12 flex-none rounded-full bg-gray-50 dark:bg-gray-700 overflow-hidden">
                                <img :src="`/storage/${provider.logo_path}`" alt="" class="h-full w-full object-cover">
                            </div>
                            <div v-else class="h-12 w-12 flex-none rounded-full bg-cyan-100 dark:bg-cyan-900 flex items-center justify-center text-cyan-600 dark:text-cyan-300 font-bold text-lg">
                                {{ provider.name.charAt(0) }}
                            </div>
                            
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm font-semibold leading-6 text-gray-900 dark:text-white flex items-center gap-2">
                                    {{ provider.name }}
                                    <span v-if="!provider.is_active" class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Inactive</span>
                                    <span v-else class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/10">Active</span>
                                </p>
                                <p class="mt-1 truncate text-xs leading-5 text-gray-500 dark:text-gray-400">
                                    <span class="capitalize">{{ provider.provider_type }}</span> • Priority: {{ provider.priority }} • Reg: {{ new Date(provider.created_at).toLocaleDateString() }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-x-4">
                            <span v-if="provider.partner_id" class="hidden sm:inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                {{ provider.partner_id }}
                            </span>
                            <button @click.stop="deleteProvider(provider)" class="text-red-600 hover:text-red-900 text-sm z-10 p-2">Delete</button>
                        </div>
                    </div>
                </li>
                 <li v-if="providers.length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
                    No providers found.
                </li>
            </ul>
        </div>
    </AdminLayout>
</template>
