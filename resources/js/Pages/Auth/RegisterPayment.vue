<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    organization: Object,
    stripeKey: String,
    paypalClientId: String
});
</script>

<template>
    <Head title="Payment Selection" />

    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 text-center">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Complete your Registration</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
                        Your organization <span class="font-bold text-blue-600">{{ organization.name }}</span> is ready to be activated.
                    </p>

                    <div class="bg-blue-50 dark:bg-blue-900/30 p-6 rounded-xl mb-8">
                         <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Selected Plan: {{ organization.hosting_mode === 'subdomain' ? 'Standard (Subdomain)' : 'Professional (Custom Domain)' }}</h3>
                         <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                            {{ organization.hosting_mode === 'subdomain' ? '$1,000 MXN' : '$1,500 MXN' }} <span class="text-sm text-gray-500 font-normal">/ month</span>
                         </p>
                         <p class="text-sm text-gray-500 mt-2">+ $200 MXN per additional admin/supervisor</p>
                    </div>

                    <div class="space-y-4">
                        <button class="w-full py-4 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition flex items-center justify-center gap-2">
                             Pay with Stripe (Credit Card)
                        </button>
                         <button class="w-full py-4 bg-yellow-400 text-blue-900 rounded-lg font-bold hover:bg-yellow-300 transition flex items-center justify-center gap-2">
                             Pay with PayPal
                        </button>
                    </div>

                    <p class="text-xs text-gray-500 mt-8">
                        By proceeding, you agree to the Terms of Service and Privacy Policy.
                        <br>
                        Commission of 5% applies to all processed sales.
                    </p>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
