<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    email: '',
    password: '',
});

const twoFactorForm = useForm({
    email: '',
    password: '',
    code: '',
});

const step = ref(1); // 1 = Login, 2 = 2FA

const submitLogin = () => {
    form.post(route('admin.login.attempt'), {
        onSuccess: (page) => {
            // Check if we got a success JSON response handling manually via Inertia?
            // Inertia usually handles page visits.
            // If the controller returned JSON, Inertia might wrap it or show as page props?
            // Actually, my controller returns JSON `response()->json(['two_factor' => true])`.
            // Inertia doesn't like direct JSON responses for page visits usually.
            // Better to return `redirect()->route('admin.login.2fa')` or similar, OR use axios for the first step.
            // But let's stick to axios for step 1 to keep it cleaner without redirection loop.
        },
        onError: () => {
            // Error handling
        }
    });
};

// Re-implementing with Axios for "Step 1" to avoid Inertia page reload issues with JSON response
import axios from 'axios';

const handleStep1 = async () => {
    form.processing = true;
    try {
        const response = await axios.post(route('admin.login.attempt'), {
            email: form.email,
            password: form.password
        });
        
        if (response.data.two_factor) {
            step.value = 2;
            twoFactorForm.email = form.email;
            twoFactorForm.password = form.password; // Keep pass to re-submit or use logic
        }
    } catch (error) {
         if (error.response && error.response.data.errors) {
            form.errors = error.response.data.errors;
        } else {
             // Fallback error
             alert('Login failed');
        }
    } finally {
        form.processing = false;
    }
};

const handleStep2 = () => {
    twoFactorForm.post(route('admin.login.verify'), {
        onFinish: () => twoFactorForm.reset('code'),
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <Head title="Admin Login" />

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-center mb-6 text-gray-800 dark:text-white">Admin Access</h2>

            <form v-if="step === 1" @submit.prevent="handleStep1">
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
                    <input v-model="form.email" type="email" class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm" required autofocus />
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                </div>

                <div class="mt-4">
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Password</label>
                    <input v-model="form.password" type="password" class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm" required />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button :disabled="form.processing" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Continue
                    </button>
                </div>
            </form>

            <form v-else @submit.prevent="handleStep2">
                 <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    A code has been sent to your email. Please enter it below.
                </div>
                
                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Two-Factor Code</label>
                    <input v-model="twoFactorForm.code" type="text" class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm" required autofocus />
                    <div v-if="twoFactorForm.errors.code" class="text-red-500 text-sm mt-1">{{ twoFactorForm.errors.code }}</div>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button :disabled="twoFactorForm.processing" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
