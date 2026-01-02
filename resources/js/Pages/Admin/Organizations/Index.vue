<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    organizations: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

// Debounce search
let timeout = null;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route('admin.organizations.index'),
            { search: value, status: status.value },
            { preserveState: true, replace: true }
        );
    }, 300);
});

const filterStatus = () => {
    router.get(
        route('admin.organizations.index'),
        { search: search.value, status: status.value },
        { preserveState: true }
    );
};

// Modal State
const showModal = ref(false);
const isEditing = ref(false);
const form = useForm({
    id: null,
    name: '',
    commercial_name: '',
    slug: '',
    is_active: true,
});

const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.is_active = true;
    showModal.value = true;
};

const openEditModal = (org) => {
    isEditing.value = true;
    form.id = org.id;
    form.name = org.name;
    form.commercial_name = org.commercial_name;
    form.slug = org.slug;
    form.is_active = !!org.is_active; // Ensure boolean
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('admin.organizations.update', form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.organizations.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteOrg = (org) => {
    if (confirm(`Are you sure you want to delete ${org.name}?`)) {
        router.delete(route('admin.organizations.destroy', org.id));
    }
};

const loginAs = (org) => {
   // Future Feature: Impersonate
   alert('Impersonation feature coming soon.');
};

</script>

<template>
    <Head title="Organizations Management" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Organizations</h2>
                <button @click="openCreateModal" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">
                    + New Organization
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Filters -->
                <div class="mb-6 flex gap-4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
                    <div class="flex-1">
                        <input
                            v-model="search"
                            type="text"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Search by name..."
                        />
                    </div>
                    <div>
                        <select v-model="status" @change="filterStatus" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-full">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                             <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Name / Legal</th>
                                    <th scope="col" class="px-6 py-3">Domain/Slug</th>
                                     <th scope="col" class="px-6 py-3">Users</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Created</th>
                                    <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="org in organizations.data" :key="org.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span>{{ org.commercial_name }}</span>
                                            <span class="text-xs text-gray-500">{{ org.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span v-if="org.hosting_mode === 'domain'" class="text-blue-600">{{ org.custom_domain }}</span>
                                        <span v-else>{{ org.slug }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ org.users_count }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span v-if="org.is_active" class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Active</span>
                                        <span v-else class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Inactive</span>
                                        
                                        <span v-if="org.subscription_status === 'suspended'" class="ml-2 px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Suspended</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ new Date(org.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button @click="openEditModal(org)" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</button>
                                        <button @click="deleteOrg(org)" class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="organizations.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center">No organizations found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <!-- Simple Pagination Implementation if component missing -->
                    <div v-if="organizations.links && organizations.links.length > 3" class="p-4 flex justify-between items-center border-t dark:border-gray-700">
                         <div class="flex gap-1" >
                            <Link 
                                v-for="(link, k) in organizations.links" 
                                :key="k"
                                :href="link.url || '#'"
                                v-html="link.label"
                                class="px-3 py-1 border rounded text-sm"
                                :class="{'bg-blue-600 text-white': link.active, 'opacity-50': !link.url}"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ isEditing ? 'Edit Organization' : 'Create Organization' }}
                </h2>

                <div class="mt-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Commercial Name</label>
                        <input
                            v-model="form.commercial_name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required
                        />
                        <div v-if="form.errors.commercial_name" class="text-red-500 text-sm mt-1">{{ form.errors.commercial_name }}</div>
                    </div>

                    <div>
                         <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Legal Name (Razon Social)</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required
                        />
                         <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>
                
                    <div>
                         <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug (Subdomain ID)</label>
                        <input
                            v-model="form.slug"
                            type="text"
                             class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm disabled:opacity-50"
                            :disabled="isEditing" 
                        />
                        <p class="text-xs text-gray-500">Leave empty to auto-generate from name.</p>
                         <div v-if="form.errors.slug" class="text-red-500 text-sm mt-1">{{ form.errors.slug }}</div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" v-model="form.is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_active" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Active</label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-4">
                    <button @click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</button>
                    <button @click="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 disabled:opacity-50">
                        {{ isEditing ? 'Update' : 'Create' }}
                    </button>
                </div>
            </div>
        </Modal>

    </AdminLayout>
</template>
