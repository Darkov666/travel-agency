<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useCartStore } from '@/Stores/cart';
import { ref } from 'vue';

const props = defineProps({
    product: Object,
    relatedProducts: Array,
});

const cart = useCartStore();
const quantity = ref(1);

const addToCart = () => {
    cart.addItem({
        id: props.product.id,
        name: props.product.title,
        price: props.product.price,
        image: props.product.image,
        type: 'merchandise' // or package
    }, quantity.value);
    // Success notification handled by MainLayout mock or toast
};
</script>

<template>
    <Head :title="product.title" />

    <MainLayout>
        <div class="py-12 bg-white dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <nav class="flex mb-8" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link href="/" class="text-gray-700 dark:text-gray-300 hover:text-primary-600">Home</Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                                <Link href="/shop" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 ml-1 md:ml-2">Shop</Link>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">{{ product.title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
                    <!-- Image Gallery -->
                    <div class="flex flex-col-reverse">
                        <div class="w-full aspect-w-1 aspect-h-1 rounded-lg overflow-hidden">
                            <img :src="product.image || 'https://placehold.co/800x600/F4A460/FFFFFF/png?text=' + product.title" :alt="product.title" class="w-full h-full object-center object-cover">
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">{{ product.title }}</h1>
                        
                        <div class="mt-3">
                            <h2 class="sr-only">Product information</h2>
                            <p class="text-3xl text-gray-900 dark:text-white">{{ product.currency }} ${{ product.price }}</p>
                        </div>

                        <div class="mt-6">
                            <h3 class="sr-only">Description</h3>
                            <div class="text-base text-gray-700 dark:text-gray-300 space-y-6">
                                <p>{{ product.description }}</p>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center space-x-4">
                            <div class="w-32">
                                <label for="quantity" class="sr-only">Quantity</label>
                                <input v-model.number="quantity" type="number" min="1" id="quantity" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Qty">
                            </div>
                            <button @click="addToCart" class="flex-1 bg-primary-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-primary-500">Add to Cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
