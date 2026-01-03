<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import SearchWidget from '@/Components/SearchWidget.vue';

defineProps({
    tenant: Object,
    services: Array,
});

const heroImage = '/images/hero.jpg'; // Valid path
</script>

<template>
    <Head :title="tenant.name" />

    <MainLayout>
        <!-- Hero -->
        <div class="relative h-[70vh] w-full overflow-hidden">
             <div class="absolute inset-0">
                <img :src="heroImage" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/50"></div>
            </div>
            
            <div class="relative h-full max-w-7xl mx-auto px-4 flex flex-col justify-center items-center text-center">
                <h1 class="text-5xl font-bold text-white mb-4">{{ tenant.commercial_name || tenant.name }}</h1>
                <p class="text-xl text-white/90">Welcome to our official booking portal.</p>
            </div>
        </div>

        <!-- Search (Mock for now, would pass zones if needed) -->
        <div class="max-w-4xl mx-auto -mt-10 relative z-10 px-4">
             <div class="bg-white rounded-xl shadow-xl p-6 text-center text-gray-500">
                 [Search Widget Placeholder - Zones need to be passed]
            </div>
        </div>

        <!-- Services -->
        <div class="py-16 max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center">Our Services</h2>
            
            <div v-if="services.length === 0" class="text-center text-gray-500">
                No services available yet.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div v-for="service in services" :key="service.id" class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="h-48 bg-gray-200"></div> <!-- Img placeholder -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2 dark:text-white">{{ service.name }}</h3>
                        <p class="text-gray-600 dark:text-gray-300 line-clamp-3">{{ service.description }}</p>
                        <div class="mt-4 text-cyan-600 font-bold">
                            From ${{ service.price_shared || 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </MainLayout>
</template>
