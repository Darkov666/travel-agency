<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { wTrans } from 'laravel-vue-i18n';

const props = defineProps({
    appointment: Object,
    service: Object,
});

const t = wTrans;
const form = useForm({
    payment_method: '',
});

const submit = () => {
    form.post(`/booking/${props.appointment.id}/payment`, {
        onSuccess: () => {
            // Redirect to confirmation or Stripe checkout
        }
    });
};
</script>

<template>
    <Head :title="t('booking.payment_title')" />
    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-secondary-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-6">
                            {{ t('booking.payment_details') }}
                        </h2>

                        <!-- Summary -->
                        <div class="bg-primary-50 dark:bg-secondary-700 p-6 rounded-lg mb-8">
                            <h3 class="text-lg font-semibold text-primary-800 dark:text-primary-200 mb-4">{{ t('booking.summary') }}</h3>
                            <div class="space-y-2 text-secondary-700 dark:text-secondary-300">
                                <div class="flex justify-between">
                                    <span>{{ t('booking.service') }}:</span>
                                    <span class="font-medium">{{ service.title }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>{{ t('booking.date') }}:</span>
                                    <span class="font-medium">{{ appointment.scheduled_at }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold text-primary-700 dark:text-primary-300 pt-2 border-t border-primary-100 dark:border-secondary-600">
                                    <span>{{ t('booking.total') }}:</span>
                                    <span>${{ service.price_mxn }} MXN</span>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <h3 class="text-lg font-medium text-secondary-900 dark:text-white mb-4">{{ t('booking.select_payment_method') }}</h3>
                            
                            <div class="space-y-4">
                                <!-- Stripe (Paused) -->
                                <!--
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-700 transition" :class="{'border-primary-500 ring-1 ring-primary-500': form.payment_method === 'stripe', 'border-gray-200 dark:border-secondary-600': form.payment_method !== 'stripe'}">
                                    <input type="radio" v-model="form.payment_method" value="stripe" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" />
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-secondary-900 dark:text-white">{{ t('booking.pay_online') }} (Stripe)</span>
                                        <span class="block text-sm text-secondary-500 dark:text-secondary-400">{{ t('booking.pay_online_desc') }}</span>
                                    </div>
                                </label>
                                -->

                                <!-- PayPal -->
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-700 transition" :class="{'border-primary-500 ring-1 ring-primary-500': form.payment_method === 'paypal', 'border-gray-200 dark:border-secondary-600': form.payment_method !== 'paypal'}">
                                    <input type="radio" v-model="form.payment_method" value="paypal" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" />
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-secondary-900 dark:text-white">PayPal</span>
                                        <span class="block text-sm text-secondary-500 dark:text-secondary-400">Pagar con PayPal (Simulación)</span>
                                    </div>
                                </label>

                                <!-- Transfer -->
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-700 transition" :class="{'border-primary-500 ring-1 ring-primary-500': form.payment_method === 'transfer', 'border-gray-200 dark:border-secondary-600': form.payment_method !== 'transfer'}">
                                    <input type="radio" v-model="form.payment_method" value="transfer" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" />
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-secondary-900 dark:text-white">{{ t('booking.bank_transfer') }}</span>
                                        <span class="block text-sm text-secondary-500 dark:text-secondary-400">{{ t('booking.bank_transfer_desc') }}</span>
                                    </div>
                                </label>
                            </div>

                            <div class="pt-6">
                                <button type="submit" :disabled="form.processing || !form.payment_method" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition disabled:opacity-50">
                                    {{ t('booking.confirm_booking') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
