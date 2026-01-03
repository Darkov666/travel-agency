<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    organization: Object,
    email: String,
});

const form = useForm({
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('partner.setup.store', props.organization.id));
};
</script>

<template>
    <Head title="Setup Account" />
    <MainLayout>
        <div class="py-12 px-4 max-w-xl mx-auto">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome, {{ organization.representative_name }}!</h2>
                    <p class="text-gray-600 dark:text-gray-300 mt-2">
                        Your payment was successful. Please set a password to access your administration dashboard for 
                        <span class="font-bold text-cyan-600">{{ organization.commercial_name }}</span>.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" :value="email" disabled class="w-full rounded-lg border-gray-300 bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Create Password</label>
                        <input v-model="form.password" type="password" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required minlength="8">
                        <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                        <input v-model="form.password_confirmation" type="password" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required minlength="8">
                    </div>

                    <button type="submit" 
                        class="w-full py-3 px-4 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-lg shadow transition disabled:opacity-50"
                        :disabled="form.processing">
                        {{ form.processing ? 'Creating Account...' : 'Complete Setup' }}
                    </button>
                </form>
            </div>
        </div>
    </MainLayout>
</template>
