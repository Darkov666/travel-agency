<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import { wTrans } from 'laravel-vue-i18n';

const props = defineProps({
    // In a real app, products would come from the backend
});

const selectedCategory = ref('all');

const categories = [
    { id: 'all', name: 'shop.all' },
    { id: 'tours', name: 'shop.tours' },
    { id: 'packages', name: 'shop.packages' },
    { id: 'merchandise', name: 'shop.merchandise' },
];

// Mock Data
const products = [
    {
        id: 101,
        title: 'Chichen Itza Deluxe Tour',
        description: 'Guided tour to the wonder of the world with buffet lunch and cenote visit included.',
        price: 129,
        currency: 'USD',
        type: 'tours',
        image: 'https://images.unsplash.com/photo-1518638151313-982d2ba5011b?q=80&w=800&auto=format&fit=crop'
    },
    {
        id: 102,
        title: 'Xcaret Plus Package',
        description: 'Full day access to Xcaret park with buffet lunch and night show.',
        price: 159,
        currency: 'USD',
        type: 'packages',
        image: 'https://images.unsplash.com/photo-1534151759604-03738dbb772c?q=80&w=800&auto=format&fit=crop'
    },
    {
        id: 103,
        title: 'Catamaran to Isla Mujeres',
        description: 'Sail the Caribbean sea, snorkel in the reef and enjoy an open bar.',
        price: 89,
        currency: 'USD',
        type: 'tours',
        image: 'https://images.unsplash.com/photo-1544551763-46a42a46e865?q=80&w=800&auto=format&fit=crop'
    },
    {
        id: 104,
        title: 'Cancun Sunny Cap',
        description: 'Exclusive branded cap to protect you from the sun in style.',
        price: 25,
        currency: 'USD',
        type: 'merchandise',
        image: 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?q=80&w=800&auto=format&fit=crop'
    },
    {
        id: 105,
        title: 'Tulum & Coba Expedition',
        description: 'Explore two ancient Mayan cities in one day. Transport and guide included.',
        price: 110,
        currency: 'USD',
        type: 'tours',
        image: 'https://images.unsplash.com/photo-1506869640319-fe1a24fd76dc?q=80&w=800&auto=format&fit=crop'
    }
];

const filteredProducts = computed(() => {
    if (selectedCategory.value === 'all') {
        return products;
    }
    return products.filter(product => product.type === selectedCategory.value);
});
</script>

<template>
    <Head :title="$t('shop.title')" />

    <MainLayout>
        <div class="py-12 bg-primary-50 dark:bg-secondary-950 min-h-screen transition-colors duration-300">
            <div class="py-12 px-4 sm:px-6 lg:px-8 text-center mb-12">
                <h1 class="text-4xl font-serif font-bold text-black mb-4">{{ $t('shop.title') }}</h1>
                <p class="text-lg text-black">{{ $t('shop.subtitle') }}</p>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Removed inner text-center div as it is now above -->

                <!-- Category Filter -->
                <div class="flex justify-center mb-10 space-x-2 sm:space-x-4 overflow-x-auto pb-4">
                    <button 
                        v-for="category in categories" 
                        :key="category.id"
                        @click="selectedCategory = category.id"
                        :class="[
                            'px-6 py-2 rounded-full text-sm font-bold transition-colors whitespace-nowrap border-2',
                            selectedCategory === category.id 
                                ? 'bg-cyan-600 border-cyan-600 text-white shadow-md' 
                                : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700'
                        ]"
                    >
                        {{ $t(category.name) }}
                    </button>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="product in filteredProducts" :key="product.id">
                        <ServiceCard :service="product" :isProduct="true" />
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredProducts.length === 0" class="text-center py-20">
                    <p class="text-secondary-500 dark:text-secondary-400 text-lg">{{ $t('shop.no_products') }}</p>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
