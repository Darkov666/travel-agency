<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { wTrans } from 'laravel-vue-i18n';

const props = defineProps({
    cart: Object,
});

const t = wTrans;

const updateQuantity = (item, quantity) => {
    if (quantity < 1) return;
    
    router.patch(route('cart.update', item.id), { quantity }, {
        preserveScroll: true,
        onSuccess: () => {
            // Success
        },
        onError: (errors) => {
            console.error('Update failed:', errors);
        }
    });
};

const removeItem = (item) => {
    if (confirm('¿Estás seguro de eliminar este servicio?')) {
        router.delete(route('cart.remove', item.id), {
            preserveScroll: true,
            onSuccess: () => {
                // router.reload({ only: ['cart'] }); // Optional redundancy
            },
            onError: (errors) => {
                console.error('Delete failed:', errors);
                alert('No se pudo eliminar el servicio.');
            }
        });
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(price);
};

const total = computed(() => {
    return props.cart?.items.reduce((sum, item) => sum + (item.service.price_mxn * item.quantity), 0) || 0;
});

const proceedToCheckout = () => {
    // Check if any item requires scheduling
    const needsScheduling = props.cart?.items.some(item => item.service.requires_scheduling);

    if (needsScheduling) {
        router.visit(route('scheduling.index'));
    } else {
        router.visit(route('checkout.index'));
    }
};
</script>

<template>
    <Head title="Carrito de Compras" />
    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-secondary-900 dark:text-white mb-8">Carrito de Compras</h1>

                <div v-if="cart && cart.items.length > 0" class="flex flex-col lg:flex-row gap-8">
                    <!-- Cart Items -->
                    <div class="flex-1 bg-white dark:bg-secondary-800 rounded-xl shadow-lg overflow-hidden">
                        <ul class="divide-y divide-gray-200 dark:divide-secondary-700">
                            <li v-for="item in cart.items" :key="item.id" class="p-6 flex items-center">
                                <img :src="item.service.image || '/images/placeholder.jpg'" alt="Service Image" class="h-24 w-24 object-cover rounded-lg border border-gray-200 dark:border-secondary-600" />
                                <div class="ml-6 flex-1">
                                    <h3 class="text-lg font-semibold text-secondary-900 dark:text-white">{{ item.service.title }}</h3>
                                    <p class="text-secondary-500 dark:text-secondary-400 text-sm">{{ item.service.duration_minutes }} min</p>
                                    <div class="mt-2 flex items-center justify-between">
                                        <div class="flex items-center border border-gray-300 dark:border-secondary-600 rounded-lg">
                                            <button @click="updateQuantity(item, item.quantity - 1)" class="px-3 py-1 text-secondary-600 dark:text-secondary-300 hover:bg-gray-100 dark:hover:bg-secondary-700">-</button>
                                            <span class="px-3 py-1 text-secondary-900 dark:text-white font-medium">{{ item.quantity }}</span>
                                            <button @click="updateQuantity(item, item.quantity + 1)" class="px-3 py-1 text-secondary-600 dark:text-secondary-300 hover:bg-gray-100 dark:hover:bg-secondary-700">+</button>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-primary-600">{{ formatPrice(item.service.price_mxn * item.quantity) }}</p>
                                            <button @click="removeItem(item)" class="text-red-500 hover:text-red-700 text-sm mt-1">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Summary -->
                    <div class="w-full lg:w-96">
                        <div class="bg-white dark:bg-secondary-800 rounded-xl shadow-lg p-6 sticky top-24">
                            <h2 class="text-xl font-bold text-secondary-900 dark:text-white mb-4">Resumen</h2>
                            <div class="flex justify-between mb-2 text-secondary-600 dark:text-secondary-300">
                                <span>Subtotal</span>
                                <span>{{ formatPrice(total) }}</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-secondary-700 my-4"></div>
                            <div class="flex justify-between mb-6 text-xl font-bold text-secondary-900 dark:text-white">
                                <span>Total</span>
                                <span>{{ formatPrice(total) }}</span>
                            </div>
                            <button @click="proceedToCheckout" class="w-full py-3 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition shadow-lg">
                                Proceder con el registro
                            </button>
                            <Link href="/services" class="block text-center mt-4 text-primary-600 hover:text-primary-700 font-medium">
                                Seguir comprando
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-20 bg-white dark:bg-secondary-800 rounded-xl shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mx-auto text-secondary-300 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-2">Tu carrito está vacío</h2>
                    <p class="text-secondary-500 dark:text-secondary-400 mb-8">Parece que aún no has agregado ningún servicio.</p>
                    <Link href="/services" class="px-8 py-3 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition shadow-lg">
                        Ver Servicios
                    </Link>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
