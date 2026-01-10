<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    categories: Array
});

const form = useForm({
    name: '',
    type: 'service',
    is_active: true
});

const editingCategory = ref(null);
const showModal = ref(false);

const create = () => {
    editingCategory.value = null;
    form.reset();
    showModal.value = true;
};

const edit = (category) => {
    editingCategory.value = category;
    form.name = category.name;
    form.type = category.type;
    form.is_active = Boolean(category.is_active);
    showModal.value = true;
};

const submit = () => {
    if (editingCategory.value) {
        form.put(route('admin.categories.update', editingCategory.value.id), {
            onSuccess: () => closeModal()
        });
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => closeModal()
        });
    }
};

const deleteCategory = (category) => {
    if (confirm('Are you sure you want to delete this category?')) {
        useForm({}).delete(route('admin.categories.destroy', category.id));
    }
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    editingCategory.value = null;
};
</script>

<template>
    <Head title="Categories" />
    <AdminLayout>
        <template #header>
            Service Categories
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Manage Categories</h3>
                    <PrimaryButton @click="create">
                        + Add Category
                    </PrimaryButton>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="category in categories" :key="category.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ category.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ category.type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ category.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <button @click="edit(category)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                    <button @click="deleteCategory(category)" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ editingCategory ? 'Edit Category' : 'New Category' }}
                </h2>

                <div class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="type" value="Type (Tag)" />
                        <TextInput id="type" v-model="form.type" type="text" class="mt-1 block w-full" placeholder="service, tour, transfer" />
                        <InputError :message="form.errors.type" class="mt-2" />
                    </div>

                    <div class="flex items-center">
                        <input id="is_active" type="checkbox" v-model="form.is_active" class="rounded border-gray-300 text-cyan-600 shadow-sm focus:border-cyan-300 focus:ring focus:ring-cyan-200 focus:ring-opacity-50">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <PrimaryButton class="ml-3" @click="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
