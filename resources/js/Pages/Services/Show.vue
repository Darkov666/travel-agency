<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    service: Object,
    relatedServices: Array,
});
</script>

<template>
    <Head :title="service.title" />

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
                                <Link href="/services" class="text-gray-700 dark:text-gray-300 hover:text-primary-600 ml-1 md:ml-2">Services</Link>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">{{ service.title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <div class="lg:grid lg:grid-cols-2 lg:gap-x-8 lg:items-start">
                    <!-- Image Gallery -->
                    <div class="flex flex-col-reverse">
                        <div class="w-full aspect-w-1 aspect-h-1 rounded-lg overflow-hidden">
                            <img :src="service.image || 'https://placehold.co/800x600/2AC1D8/FFFFFF/png?text=' + service.title" :alt="service.title" class="w-full h-full object-center object-cover">
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                        <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">{{ service.title }}</h1>
                        
                        <div class="mt-3">
                            <h2 class="sr-only">Product information</h2>
                            <p class="text-3xl text-gray-900 dark:text-white">{{ service.currency }} ${{ service.price }}</p>
                        </div>

                        <div class="mt-6">
                            <h3 class="sr-only">Description</h3>
                            <div class="text-base text-gray-700 dark:text-gray-300 space-y-6">
                                <p>{{ service.description }}</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <p class="ml-2 text-sm text-gray-500">Instant Confirmation</p>
                            </div>
                        </div>

                        <div class="mt-10 flex sm:flex-col1">
                            <button class="max-w-xs flex-1 bg-primary-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-primary-500 sm:w-full">Book Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
