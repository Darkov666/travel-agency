<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

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
const showPassword = ref(false);
const loginError = ref(null);

const submitLogin = () => {
    // ...
};

const handleStep1 = async () => {
    form.processing = true;
    loginError.value = null;
    form.clearErrors(); // Inertia helper, but using axios directly might need clear.
    
    try {
        const response = await axios.post(route('admin.login.attempt'), {
            email: form.email,
            password: form.password
        }, {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (response.data.two_factor) {
            step.value = 2;
            twoFactorForm.email = form.email;
            twoFactorForm.password = form.password; 
        }
    } catch (error) {
        console.error("Login Error", error);
         if (error.response && error.response.data.errors) {
            form.errors = error.response.data.errors;
        } else if (error.response && error.response.data.message) {
             loginError.value = error.response.data.message;
        } else {
             loginError.value = 'Credenciales incorrectas o error de conexión.';
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
        <Head title="Admin Login_Fixed" />

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
                    <div class="relative">
                        <input v-model="form.password" :type="showPassword ? 'text' : 'password'" class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-md shadow-sm pr-10" required />
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none mt-1">
                             <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                     <div v-if="form.errors.email" class="text-red-500 text-sm mt-1 font-bold">{{ form.errors.email }}</div>
                     <div v-if="form.errors.message" class="text-red-500 text-sm mt-1 font-bold">{{ form.errors.message }}</div>
                     <!-- General error fallback -->
                     <div v-if="loginError" class="text-red-500 text-sm mt-1 font-bold">{{ loginError }}</div>
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
