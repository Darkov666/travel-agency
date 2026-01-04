<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Partners" />

    <MainLayout>
        <div class="py-12 bg-white dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                        Our Partners
                    </h2>
                    <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500 dark:text-gray-400">
                        Join our network of trusted providers and grow your business with us.
                    </p>
                </div>

                <div class="mt-12 bg-primary-50 dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden lg:grid lg:grid-cols-2 lg:gap-4">
                    <div class="pt-10 pb-12 px-6 sm:pt-16 sm:px-16 lg:py-16 lg:pr-0 xl:py-20 xl:px-20">
                        <div class="lg:self-center">
                            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                                <span class="block">Ready to dive in?</span>
                                <span class="block text-primary-600 dark:text-primary-400">Start your free trial today.</span>
                            </h2>
                            <p class="mt-4 text-lg leading-6 text-gray-500 dark:text-gray-300">
                                Become a partner and access our exclusive platform for managing bookings, services, and more.
                            </p>
                            <a href="/partner/register" class="mt-8 bg-primary-600 border border-transparent rounded-md shadow px-5 py-3 inline-flex items-center text-base font-medium text-white hover:bg-primary-700">
                                Register as Partner
                            </a>
                        </div>
                    </div>
                     <div class="-mt-6 aspect-w-5 aspect-h-3 md:aspect-w-2 md:aspect-h-1">
                         <img class="transform translate-x-6 translate-y-6 rounded-md object-cover object-left-top sm:translate-x-16 lg:translate-y-20 h-full w-full opacity-80" src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1567&q=80" alt="App screenshot">
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
