<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

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
</script>

<template>
    <Head title="Search Results" />

    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-serif font-bold text-secondary-900 dark:text-white">
                        Access to {{ searchParams.destination }}
                    </h1>
                    <p class="text-secondary-600 dark:text-secondary-400 mt-2">
                        Found {{ results.length }} options for {{ searchParams.pax }} passengers
                    </p>
                </div>

                <!-- Results Grid -->
                <div v-if="results.length > 0" class="grid gap-6">
                    <div v-for="tariff in results" :key="tariff.id" class="bg-white dark:bg-secondary-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition duration-300 border border-gray-100 dark:border-secondary-700">
                        <div class="p-6 flex flex-col md:flex-row md:items-center justify-between">
                            <!-- Provider Info -->
                            <div class="flex items-center mb-4 md:mb-0">
                                <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-300 font-bold text-xl mr-4">
                                    {{ tariff.provider.name.charAt(0) }}
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-secondary-900 dark:text-white">{{ tariff.provider.name }}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ tariff.service_type }}
                                    </span>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="text-center md:text-left mb-4 md:mb-0">
                                <p class="text-sm text-secondary-500 dark:text-secondary-400">Capacity</p>
                                <p class="font-semibold text-secondary-900 dark:text-white">{{ tariff.pax }} Pax</p>
                            </div>

                             <!-- Price & Action -->
                            <div class="flex flex-col md:items-end">
                                <p class="text-sm text-secondary-500 dark:text-secondary-400">Total Price</p>
                                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400 mb-2">
                                    {{ formatCurrency(tariff.price) }}
                                </p>
                                <button class="px-6 py-2 bg-secondary-900 hover:bg-black text-white rounded-full font-bold transition">
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
                    <p class="text-secondary-600 dark:text-secondary-400">
                        We couldn't find any transfers for this combination. Try changing the number of passengers or destination.
                    </p>
                     <Link href="/" class="inline-block mt-8 px-8 py-3 bg-primary-600 text-white rounded-full font-bold">
                        Go Back
                    </Link>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
