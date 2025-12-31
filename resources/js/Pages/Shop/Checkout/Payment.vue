<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    reservation: Object,
    paypalClientId: String,
});

const paymentMethod = ref('transfer'); // default
const paymentChoice = ref('deposit'); 

const form = useForm({
    payment_method: 'transfer',
    payment_choice: 'deposit'
});

const submitPayment = () => {
    form.payment_method = paymentMethod.value;
    form.payment_choice = paymentChoice.value;
    form.post(route('checkout.process_payment', props.reservation.booking_ref));
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

const amountToPayNow = computed(() => {
    if (paymentMethod.value === 'cash') {
        // Cash always deposit only? User said: "All services charged upfront commission... or full amount via pay pal. also can pay rest in cash".
        // Logic: Transfer/PayPal can be Full or Split. Cash is Deposit Only (since rest is cash on arrival).
        return props.reservation.total_amount * 0.20;
    }
    
    if (paymentChoice.value === 'deposit') {
        return props.reservation.total_amount * 0.20;
    }
    return props.reservation.total_amount;
});
</script>

<script>
    import { computed } from 'vue';
</script>

<template>
    <Head title="Checkout - Payment" />

    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
             <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-serif font-bold text-secondary-900 dark:text-white mb-8">
                    Payment Method
                </h1>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Methods -->
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
                             <!-- Options -->
                             <div class="space-y-4">
                                <label class="flex items-center p-4 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-700" :class="{'border-primary-500 ring-1 ring-primary-500': paymentMethod === 'transfer', 'border-gray-200 dark:border-gray-600': paymentMethod !== 'transfer'}">
                                    <input type="radio" value="transfer" v-model="paymentMethod" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                                    <div class="ml-4">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Bank Transfer (Deposit)</span>
                                        <span class="block text-sm text-gray-500">Pay {{ formatCurrency(depositAmount) }} now, balance in cash/transfer later.</span>
                                    </div>
                                </label>

                                <label class="flex items-center p-4 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-700" :class="{'border-primary-500 ring-1 ring-primary-500': paymentMethod === 'paypal', 'border-gray-200 dark:border-gray-600': paymentMethod !== 'paypal'}">
                                    <input type="radio" value="paypal" v-model="paymentMethod" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                                    <div class="ml-4">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">PayPal</span>
                                        <span class="block text-sm text-gray-500">Pay Full Amount {{ formatCurrency(reservation.total_amount) }}.</span>
                                    </div>
                                </label>

                                <label class="flex items-center p-4 border rounded-lg cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-700" :class="{'border-primary-500 ring-1 ring-primary-500': paymentMethod === 'cash', 'border-gray-200 dark:border-gray-600': paymentMethod !== 'cash'}">
                                    <input type="radio" value="cash" v-model="paymentMethod" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                                    <div class="ml-4">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Cash / Effective</span>
                                        <span class="block text-sm text-gray-500">Pay {{ formatCurrency(depositAmount) }} deposit now required.</span>
                                    </div>
                                </label>
                             </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="md:col-span-1">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700 sticky top-4">
                            <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-4">Payment Summary</h3>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600 dark:text-gray-300">Total Booking</span>
                                <span class="text-gray-900 dark:text-white font-bold">{{ formatCurrency(reservation.total_amount) }}</span>
                            </div>
                            
                            <!-- Payment Choice (If Transfer/Paypal) -->
                            <div v-if="paymentMethod !== 'cash'" class="my-4">
                                <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">How much do you want to pay now?</p>
                                <div class="flex space-x-2">
                                     <label class="flex-1 cursor-pointer">
                                        <input type="radio" value="deposit" v-model="paymentChoice" class="peer sr-only">
                                        <div class="text-center p-2 rounded-lg border border-gray-200 dark:border-gray-600 peer-checked:bg-primary-50 peer-checked:border-primary-500 dark:peer-checked:bg-primary-900 peer-checked:text-primary-700 dark:peer-checked:text-primary-300 text-xs">
                                            Deposit Only (20%)
                                        </div>
                                     </label>
                                     <label class="flex-1 cursor-pointer">
                                        <input type="radio" value="full" v-model="paymentChoice" class="peer sr-only">
                                        <div class="text-center p-2 rounded-lg border border-gray-200 dark:border-gray-600 peer-checked:bg-primary-50 peer-checked:border-primary-500 dark:peer-checked:bg-primary-900 peer-checked:text-primary-700 dark:peer-checked:text-primary-300 text-xs">
                                            Full Amount
                                        </div>
                                     </label>
                                </div>
                            </div>
                            
                            <hr class="my-4 border-gray-200 dark:border-gray-700">

                            <div v-if="amountToPayNow < reservation.total_amount" class="space-y-2">
                                <div class="flex justify-between text-sm text-primary-600 dark:text-cyan-400 font-bold">
                                    <span>Pay Now</span>
                                    <span>{{ formatCurrency(amountToPayNow) }}</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Balance Due Later</span>
                                    <span>{{ formatCurrency(reservation.total_amount - amountToPayNow) }}</span>
                                </div>
                                <p class="text-xs text-orange-600 bg-orange-50 dark:bg-orange-900/30 p-2 rounded mt-2">
                                    Note: Returns must be paid at the time of the first service (arrival).
                                </p>
                            </div>
                             <div v-else class="flex justify-between text-lg font-bold text-primary-600 dark:text-cyan-400">
                                <span>Pay Now</span>
                                <span>{{ formatCurrency(reservation.total_amount) }}</span>
                            </div>

                            <button @click="submitPayment" :disabled="form.processing" class="w-full mt-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-full font-bold shadow-lg transition duration-200 disabled:opacity-50">
                                <span v-if="paymentMethod === 'paypal'"> Simulate PayPal Payment (Success)</span>
                                <span v-else>Confirm & View Transfer Instructions</span>
                            </button>
                        </div>
                    </div>
                </div>
             </div>
        </div>
    </MainLayout>
</template>
