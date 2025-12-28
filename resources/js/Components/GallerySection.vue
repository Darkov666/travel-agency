<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
        // Expected format: [{ title: 'Category Name', images: ['url1', 'url2', ...] }]
    }
});

const currentImage = ref(null);
const isLightboxOpen = ref(false);

// Carousel state per category
const carouselIndices = ref({});

const initCarousels = () => {
    props.categories.forEach((cat, index) => {
        carouselIndices.value[index] = 0;
    });
};

initCarousels();

const nextSlide = (categoryIndex, totalImages) => {
    carouselIndices.value[categoryIndex] = (carouselIndices.value[categoryIndex] + 1) % totalImages;
};

const prevSlide = (categoryIndex, totalImages) => {
    carouselIndices.value[categoryIndex] = (carouselIndices.value[categoryIndex] - 1 + totalImages) % totalImages;
};

const openLightbox = (image) => {
    currentImage.value = image;
    isLightboxOpen.value = true;
    document.body.style.overflow = 'hidden'; // Prevent scrolling
};

const closeLightbox = () => {
    isLightboxOpen.value = false;
    currentImage.value = null;
    document.body.style.overflow = '';
};
</script>

<template>
    <section class="py-16 bg-white dark:bg-secondary-900 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-serif font-bold text-secondary-900 dark:text-white">{{ $t('gallery.title') }}</h2>
                <p class="mt-4 text-lg text-secondary-500 dark:text-secondary-400">{{ $t('gallery.subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div v-for="(category, index) in categories" :key="index" class="space-y-4">
                    <h3 class="text-2xl font-serif font-semibold text-secondary-800 dark:text-secondary-100 text-center">{{ $t(category.title) }}</h3>
                    
                    <div class="relative group rounded-xl overflow-hidden shadow-lg aspect-video bg-secondary-100 dark:bg-secondary-800">
                        <!-- Carousel Images -->
                        <div class="w-full h-full relative">
                            <div 
                                v-for="(image, imgIndex) in category.images" 
                                :key="imgIndex"
                                class="absolute inset-0 w-full h-full transition-opacity duration-500 ease-in-out cursor-pointer"
                                :class="{ 'opacity-100 z-10': carouselIndices[index] === imgIndex, 'opacity-0 z-0': carouselIndices[index] !== imgIndex }"
                                @click="openLightbox(image)"
                            >
                                <img :src="image" :alt="category.title" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                            </div>
                        </div>

                        <!-- Navigation Controls -->
                        <button 
                            @click.stop="prevSlide(index, category.images.length)"
                            class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity z-20"
                            aria-label="Previous slide"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button 
                            @click.stop="nextSlide(index, category.images.length)"
                            class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity z-20"
                            aria-label="Next slide"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Dots Indicator -->
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2 z-20">
                            <button 
                                v-for="(_, dotIndex) in category.images" 
                                :key="dotIndex"
                                @click.stop="carouselIndices[index] = dotIndex"
                                class="w-2 h-2 rounded-full transition-colors duration-300"
                                :class="carouselIndices[index] === dotIndex ? 'bg-white' : 'bg-white/50 hover:bg-white/80'"
                            ></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lightbox Overlay -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isLightboxOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm" @click="closeLightbox">
                <button class="absolute top-4 right-4 text-white hover:text-gray-300 z-50 p-2" @click="closeLightbox">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="relative max-w-5xl max-h-[90vh] p-4" @click.stop>
                    <img :src="currentImage" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl" alt="Full screen view">
                </div>
            </div>
        </Transition>
    </section>
</template>
