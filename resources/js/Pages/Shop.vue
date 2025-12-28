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
    { id: 'ebook', name: 'shop.ebooks' },
    { id: 'video', name: 'shop.videos' },
    { id: 'manual', name: 'shop.manuals' },
];

// Mock Data
const products = [
    {
        id: 101,
        title: 'Guía de Ansiedad',
        description: 'Un eBook completo para entender y manejar la ansiedad en el día a día.',
        price: 299,
        currency: 'MXN',
        type: 'ebook',
        image: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=800&auto=format&fit=crop'
    },
    {
        id: 102,
        title: 'Masterclass: Autoestima',
        description: 'Video curso de 2 horas sobre cómo construir una autoestima sólida.',
        price: 599,
        currency: 'MXN',
        type: 'video',
        image: 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=800&auto=format&fit=crop',
        video_url: 'https://media.giphy.com/media/v1.Y2lkPTc5MGI3NjExbDN6eW56eW56eW56eW56eW56eW56eW56eW56eW56eW56eSZlcD12MV9pbnRlcm5hbF9naWZfYnlfaWQmY3Q9Zw/3o7TKr3nzbh5WgCFxe/giphy.gif'
    },
    {
        id: 103,
        title: 'Manual de Terapia Cognitiva',
        description: 'Manual técnico para estudiantes y profesionales de la salud mental.',
        price: 450,
        currency: 'MXN',
        type: 'manual',
        image: 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?q=80&w=800&auto=format&fit=crop'
    },
    {
        id: 104,
        title: 'Meditaciones Guiadas',
        description: 'Pack de 10 videos con meditaciones para dormir y relajarse.',
        price: 350,
        currency: 'MXN',
        type: 'video',
        image: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=800&auto=format&fit=crop'
    },
    {
        id: 105,
        title: 'Diario de Gratitud',
        description: 'eBook interactivo para practicar la gratitud diariamente.',
        price: 150,
        currency: 'MXN',
        type: 'ebook',
        image: 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?q=80&w=800&auto=format&fit=crop'
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
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-serif font-bold text-secondary-900 dark:text-white mb-4">{{ $t('shop.title') }}</h1>
                    <p class="text-lg text-secondary-600 dark:text-secondary-400">{{ $t('shop.subtitle') }}</p>
                </div>

                <!-- Category Filter -->
                <div class="flex justify-center mb-10 space-x-2 sm:space-x-4 overflow-x-auto pb-4">
                    <button 
                        v-for="category in categories" 
                        :key="category.id"
                        @click="selectedCategory = category.id"
                        :class="[
                            'px-6 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap',
                            selectedCategory === category.id 
                                ? 'bg-primary-600 text-white shadow-md' 
                                : 'bg-white dark:bg-secondary-800 text-secondary-600 dark:text-secondary-300 hover:bg-primary-50 dark:hover:bg-secondary-700'
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
