<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';

const selectedCategory = ref('all');

const categories = [
    { id: 'all', name: 'services.all' },
    { id: 'transfer', name: 'services.transfers' },
    { id: 'tour', name: 'services.tours' },
    { id: 'private', name: 'services.private' },
];

const props = defineProps({
    services: Array
});

// Mock Data removed - using props.services instead

const filteredServices = computed(() => {
    if (selectedCategory.value === 'all') {
        return props.services;
    }
    return props.services.filter(service => {
        if (selectedCategory.value === 'workshops') {
            return ['workshop', 'conference', 'talk', 'training'].includes(service.type);
        }
        
        return service.type === selectedCategory.value || 
               (service.title && service.title.toLowerCase().includes(selectedCategory.value));
    });
});
</script>

<template>
    <Head :title="$t('services.title')" />

    <MainLayout>
        <div class="py-12 bg-primary-50 dark:bg-secondary-950 min-h-screen transition-colors duration-300">
            <div class="py-12 px-4 sm:px-6 lg:px-8 text-center mb-12">
                <h1 class="text-4xl font-serif font-bold text-black mb-4">{{ $t('services.title') }}</h1>
                <p class="text-lg text-black">{{ $t('services.subtitle') }}</p>
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

                <!-- Services Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="service in filteredServices" :key="service.id">
                        <ServiceCard :service="service" />
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredServices.length === 0" class="text-center py-20">
                    <p class="text-secondary-500 dark:text-secondary-400 text-lg">{{ $t('shop.no_products') }}</p>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
