<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useCartStore } from '@/Stores/cart';
import { computed } from 'vue';

const cartStore = useCartStore();

const currencySymbol = computed(() => cartStore.currency === 'MXN' ? 'MX$' : 'US$');

const removeItem = (index) => {
    cartStore.removeFromCart(index);
};
</script>

<template>
    <Head title="Shopping Cart" />

    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-serif font-bold text-secondary-900 dark:text-white mb-8">Tu Carrito de Compras</h1>

                <div v-if="cartStore.items.length === 0" class="text-center py-12 bg-white dark:bg-secondary-800 rounded-lg shadow">
                    <p class="text-lg text-secondary-500 dark:text-secondary-400 mb-6">Tu carrito está vacío.</p>
                    <Link href="/services" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition">
                        Ver Servicios
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4">
                        <div v-for="(item, index) in cartStore.items" :key="index" class="bg-white dark:bg-secondary-800 p-6 rounded-lg shadow flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-secondary-900 dark:text-white">{{ item.title }}</h3>
                                <p class="text-sm text-secondary-500 dark:text-secondary-400">{{ item.duration_minutes }} minutos</p>
                            </div>
                            <div class="flex items-center space-x-6">
                                <span class="text-lg font-semibold text-primary-700 dark:text-primary-400">
                                    {{ currencySymbol }}{{ cartStore.currency === 'MXN' ? item.price_mxn : item.price_usd }}
                                </span>
                                <button @click="removeItem(index)" class="text-red-500 hover:text-red-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-secondary-800 p-6 rounded-lg shadow sticky top-24">
                            <h2 class="text-xl font-bold text-secondary-900 dark:text-white mb-6">Resumen del Pedido</h2>
                            <div class="flex justify-between items-center mb-6 text-lg font-semibold">
                                <span class="text-secondary-700 dark:text-secondary-300">Total:</span>
                                <span class="text-primary-700 dark:text-primary-400">{{ currencySymbol }}{{ cartStore.cartTotal }}</span>
                            </div>
                            <Link href="/checkout" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition shadow-md">
                                Proceder al Pago
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
