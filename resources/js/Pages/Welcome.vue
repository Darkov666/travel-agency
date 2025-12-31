<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import SearchWidget from '@/Components/SearchWidget.vue';
import ContactSection from '@/Components/ContactSection.vue';
// import ServiceCard from '@/Components/ServiceCard.vue'; // Temporarily disabled until updated
// import { wTrans } from 'laravel-vue-i18n';

defineProps({
    featuredServices: Array,
    latestPosts: Array,
    zones: Array,
});

// Using local image to ensure reliability
const heroImage = '/images/hero.jpg';
const vanImage = 'https://placehold.co/800x600/F4A460/FFFFFF/png?text=Luxury+Van';
const ruinsImage = 'https://placehold.co/800x600/FF7F50/FFFFFF/png?text=Mayan+Ruins';

</script>

<template>
    <Head title="Welcome to Paradise" />

    <MainLayout>
        <!-- Hero Section -->
        <div class="relative h-[85vh] w-full overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0">
                <img :src="heroImage" alt="Tulum Ruins by Seaside" class="w-full h-full object-cover">
                <!-- Stronger Gradient for text readability -->
                <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/70"></div>
            </div>

            <!-- Content -->
            <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col justify-center items-center text-center pt-20 pb-24">
                <h1 class="text-5xl md:text-7xl font-serif font-bold text-white drop-shadow-xl tracking-tight mb-4 animate-fade-in-up">
                    Experience <span class="text-secondary-300">Paradise</span> <br class="hidden md:block" /> with Comfort
                </h1>
                <p class="text-xl md:text-2xl text-white/95 max-w-2xl font-light mb-12 drop-shadow-md animate-fade-in-up delay-100">
                    Premium airport transfers and exclusive tours in Cancun & Riviera Maya.
                </p>
                
                <div class="animate-fade-in-up delay-200">
                    <Link href="/services" class="inline-flex items-center px-8 py-3 border-2 border-white text-base font-bold rounded-full text-white hover:bg-white hover:text-primary-600 transition-all duration-300 backdrop-blur-sm bg-white/10">
                        Explore Services
                    </Link>
                </div>
            </div>
        </div>

        <!-- Search Widget (Floating overlap) -->
        <SearchWidget :zones="zones" />

        <!-- Value Propositions -->
        <div class="py-16 bg-primary-50 dark:bg-gray-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="p-6 rounded-xl bg-white dark:bg-gray-800 hover:shadow-lg transition">
                        <div class="w-16 h-16 mx-auto bg-cyan-100 dark:bg-cyan-900 rounded-full flex items-center justify-center mb-4 text-cyan-600 dark:text-cyan-400">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Punctuality Guaranteed</h3>
                        <p class="text-gray-600 dark:text-gray-300">We track your flight to ensure we are there when you land. No waiting.</p>
                    </div>
                     <div class="p-6 rounded-xl bg-white dark:bg-gray-800 hover:shadow-lg transition">
                        <div class="w-16 h-16 mx-auto bg-cyan-100 dark:bg-cyan-900 rounded-full flex items-center justify-center mb-4 text-cyan-600 dark:text-cyan-400">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Safe & Secure</h3>
                        <p class="text-gray-600 dark:text-gray-300">Certified drivers and sanitized luxury vehicles for your peace of mind.</p>
                    </div>
                     <div class="p-6 rounded-xl bg-white dark:bg-gray-800 hover:shadow-lg transition">
                        <div class="w-16 h-16 mx-auto bg-cyan-100 dark:bg-cyan-900 rounded-full flex items-center justify-center mb-4 text-cyan-600 dark:text-cyan-400">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Best Price</h3>
                        <p class="text-gray-600 dark:text-gray-300">Competitive rates with no hidden fees. Luxury service at fair prices.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Section -->
        <div class="py-20 bg-secondary-50 dark:bg-black overflow-hidden relative transition-colors duration-300">
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                    <div>
                        <h2 class="text-4xl font-serif font-bold text-gray-900 dark:text-white mb-6">Explore the Ancient World</h2>
                        <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
                            Discover the magic of Chichen Itza, Tulum, and Coba with our private tours. We take you there in comfort and style, allowing you to explore at your own pace.
                        </p>
                        <Link href="/services" class="text-cyan-600 dark:text-cyan-400 font-bold hover:text-cyan-700 dark:hover:text-cyan-300 inline-flex items-center transition-colors duration-300">
                            View All Tours <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </Link>
                    </div>
                    <div class="mt-10 lg:mt-0 relative">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl transform hover:scale-[1.02] transition duration-500">
                            <img :src="ruinsImage" alt="Mayan Ruins" class="w-full h-auto object-cover">
                        </div>
                         <!-- Decorative blob -->
                        <div class="absolute -z-10 -bottom-10 -right-10 w-64 h-64 bg-secondary-200 dark:bg-secondary-800 rounded-full opacity-50 filter blur-3xl"></div>
                    </div>
                </div>
            </div>
        </div>

         <!-- Vehicle Fleet Section -->
        <div class="py-20 bg-primary-50 dark:bg-gray-900 transition-colors duration-300">
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center flex-row-reverse">
                    <div class="order-2 lg:order-1 mt-10 lg:mt-0">
                         <div class="relative rounded-2xl overflow-hidden shadow-2xl transform hover:scale-[1.02] transition duration-500">
                            <img :src="vanImage" alt="Luxury Van" class="w-full h-auto object-cover">
                        </div>
                    </div>
                    <div class="order-1 lg:order-2">
                        <h2 class="text-4xl font-serif font-bold text-gray-900 dark:text-white mb-6">Travel in Luxury</h2>
                        <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">
                            Our fleet consists of modern, air-conditioned vehicles including private vans and luxury SUVs. Enjoy complimentary water and a smooth ride to your destination.
                        </p>
                         <ul class="space-y-4 mb-8">
                            <li class="flex items-center text-secondary-700 dark:text-gray-200">
                                <span class="bg-primary-100 text-primary-600 rounded-full p-1 mr-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                                Private Air-Conditioned Vans
                            </li>
                             <li class="flex items-center text-secondary-700 dark:text-gray-200">
                                <span class="bg-primary-100 text-primary-600 rounded-full p-1 mr-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                                Professional Bilingual Drivers
                            </li>
                             <li class="flex items-center text-secondary-700 dark:text-gray-200">
                                <span class="bg-primary-100 text-primary-600 rounded-full p-1 mr-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                                Flight Monitoring included
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Section -->
        <ContactSection />

    </MainLayout>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
    opacity: 0;
    transform: translateY(20px);
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

