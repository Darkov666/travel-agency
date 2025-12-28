<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ServiceCard from '@/Components/ServiceCard.vue';

const props = defineProps({
    services: Array
});

// No category filter for now, just show all special services
</script>

<template>
    <Head :title="$t('gallery.workshops')" />

    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-serif font-bold text-secondary-900 dark:text-white mb-4">{{ $t('gallery.workshops') }}</h1>
                    <p class="text-lg text-secondary-600 dark:text-secondary-400">
                        Talleres, Conferencias y Pláticas Especializadas
                    </p>
                </div>

                <!-- Services Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="service in services" :key="service.id">
                        <ServiceCard :service="service" :isSpecial="true" />
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="services.length === 0" class="text-center py-20">
                    <p class="text-secondary-500 dark:text-secondary-400 text-lg">{{ $t('shop.no_products') }}</p>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
