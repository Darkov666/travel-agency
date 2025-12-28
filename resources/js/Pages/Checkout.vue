<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useCartStore } from '@/Stores/cart';
import { computed, ref } from 'vue';

const cartStore = useCartStore();
const currencySymbol = computed(() => cartStore.currency === 'MXN' ? 'MX$' : 'US$');

const isProcessing = ref(false);

const processPayment = () => {
    isProcessing.value = true;
    // Mock payment process
    setTimeout(() => {
        alert('¡Pago procesado con éxito! (Simulación)');
        cartStore.clearCart();
        window.location.href = '/';
    }, 2000);
};
</script>

<template>
    <Head title="Checkout" />

    <MainLayout>
        <div class="py-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-serif font-bold text-secondary-900 dark:text-white mb-8 text-center">Finalizar Compra</h1>

                <div class="bg-white dark:bg-secondary-800 rounded-lg shadow overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-xl font-semibold text-secondary-900 dark:text-white mb-6">Detalles del Pago</h2>
                        
                        <!-- Mock Stripe Form -->
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">Nombre en la tarjeta</label>
                                <input type="text" class="w-full rounded-md border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="Juan Pérez">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">Número de tarjeta</label>
                                <div class="relative">
                                    <input type="text" class="w-full rounded-md border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 pl-10" placeholder="0000 0000 0000 0000">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">Fecha de expiración</label>
                                    <input type="text" class="w-full rounded-md border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="MM/YY">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">CVC</label>
                                    <input type="text" class="w-full rounded-md border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500" placeholder="123">
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-8 border-t border-secondary-200 dark:border-secondary-700">
                            <div class="flex justify-between items-center mb-6">
                                <span class="text-lg font-medium text-secondary-700 dark:text-secondary-300">Total a Pagar:</span>
                                <span class="text-2xl font-bold text-primary-700 dark:text-primary-400">{{ currencySymbol }}{{ cartStore.cartTotal }}</span>
                            </div>
                            
                            <button 
                                @click="processPayment" 
                                :disabled="isProcessing"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition"
                            >
                                <svg v-if="isProcessing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ isProcessing ? 'Procesando...' : 'Pagar Ahora' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
