<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps({
    results: Array,
    searchParams: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
};

const addToCart = async (tariff, redirect = false) => {
    try {
        await axios.post(route('cart.add'), {
            id: tariff.id, // Service ID (provider_service id)
            type: 'transfer', 
            pax: props.searchParams.pax,
            date: props.searchParams.date,
            return_date: props.searchParams.return_date,
            units: tariff.units,
            price: tariff.price
        });
        
        if (redirect) {
             // Go to checkout
             // We use Inertia router to visit
             // Import router first! 
             // Wait, I can't import inside function. I need to update imports.
             window.location.href = route('checkout.index'); // Simple redirect or use Inertia
        } else {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Added to cart',
                showConfirmButton: false,
                timer: 3000
            });
        }
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Could not add to cart', 'error');
    }
};

const bookNow = (tariff) => {
    addToCart(tariff, true);
};
</script>

<template>
    <Head title="Search Results" />

    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-serif font-bold text-secondary-900 dark:text-white">
                        Access to {{ searchParams.destination }}
                    </h1>
                    <p class="text-secondary-600 dark:text-white mt-2">
                        Found {{ results.length }} options for {{ searchParams.pax }} passengers
                    </p>
                </div>

                <!-- Results Grid -->
                <div v-if="results.length > 0" class="grid gap-6">
                    <div v-for="tariff in results" :key="tariff.id" class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300 border border-gray-100 dark:border-gray-700">
                        <div class="p-6 flex flex-col md:flex-row md:items-center justify-between">
                            <!-- Provider Info / Vehicle Image -->
                            <div class="flex items-center mb-4 md:mb-0">
                                <div class="w-24 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden flex items-center justify-center mr-4">
                                     <img v-if="tariff.vehicle_image" :src="`/storage/${tariff.vehicle_image}`" class="w-full h-full object-cover">
                                     <div v-else class="text-xs text-gray-500 dark:text-gray-400">No Image</div>
                                </div>
                                <div>
                                    <!-- Provider Name Hidden as requested -->
                                    <h3 class="text-xl font-bold text-secondary-900 dark:text-white">{{ tariff.service_type }}</h3>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="text-center md:text-left mb-4 md:mb-0">
                                <p class="text-sm text-secondary-500 dark:text-gray-400">Capacity</p>
                                <p class="font-semibold text-secondary-900 dark:text-gray-100">{{ tariff.pax }} Pax</p>
                            </div>

                             <!-- Price & Action -->
                            <div class="flex flex-col md:items-end">
                                <p class="text-sm text-secondary-500 dark:text-gray-400">Total Price</p>
                                <p class="text-3xl font-bold text-primary-600 dark:text-cyan-400 mb-2">
                                    {{ formatCurrency(tariff.price) }}
                                </p>
                                <button @click="addToCart(tariff)" class="w-full mb-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-bold transition dark:bg-blue-500 dark:hover:bg-blue-600">
                                    Add to Cart
                                </button>
                                <button @click="bookNow(tariff)" class="w-full px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-full font-bold transition dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Results -->
                <div v-else class="text-center py-20">
                    <div class="text-6xl mb-4">😢</div>
                    <h3 class="text-2xl font-bold text-secondary-900 dark:text-white mb-2">No transfers found</h3>
                    <p class="text-secondary-600 dark:text-white">
                        We couldn't find any transfers for this combination. Try changing the number of passengers or destination.
                    </p>
                    <Link href="/" class="inline-block mt-8 px-8 py-3 bg-blue-600 text-white rounded-full font-bold shadow-lg hover:bg-blue-700 dark:bg-white dark:text-black dark:hover:bg-gray-200 transition">
                        Go Back
                    </Link>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
