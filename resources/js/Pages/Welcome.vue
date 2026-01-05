<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import SearchWidget from '@/Components/SearchWidget.vue';
import ContactSection from '@/Components/ContactSection.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
// import { wTrans } from 'laravel-vue-i18n';

defineProps({
    featuredServices: Array,
    latestPosts: Array,
    latestReviews: Array,
    zones: Array,
    contentBlocks: Object,
});

// Using local image to ensure reliability
// Image defaults now handled via contentBlocks logic
const defaultHero = '/images/hero.jpg';
const defaultVan = 'https://placehold.co/800x600/F4A460/FFFFFF/png?text=Luxury+Van';
const defaultRuins = 'https://placehold.co/800x600/FF7F50/FFFFFF/png?text=Mayan+Ruins';

const safeJsonParse = (str) => {
    try {
        return JSON.parse(str);
    } catch (e) {
        return null;
    }
};

</script>

<template>
    <Head title="Welcome to Paradise" />

    <MainLayout>
        <!-- Hero Section -->
        <div class="relative h-[85vh] w-full overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0">
                <img :src="(contentBlocks && contentBlocks['home_hero_image']) ? contentBlocks['home_hero_image'] : defaultHero" alt="Hero Background" class="w-full h-full object-cover">
                <!-- Stronger Gradient for text readability -->
                <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/70"></div>
            </div>

            <!-- Content -->
            <!-- Content -->
            <!-- Content -->
            <div class="relative h-full flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 z-10" :class="[contentBlocks['home_hero_align'] || 'text-center']">
                <h1 class="font-bold text-white mb-6 drop-shadow-lg tracking-tight animate-fade-in-up" :class="[contentBlocks['home_hero_font_family'] || 'font-serif', contentBlocks['home_hero_title_size'] || 'text-4xl sm:text-5xl md:text-7xl']">
                    {{ (contentBlocks && contentBlocks['home_hero_title']) ? contentBlocks['home_hero_title'] : 'Discover the Riviera Maya' }}
                </h1>
                <p class="text-white/90 max-w-3xl mb-10 drop-shadow-md font-light animate-fade-in-up delay-100" :class="[contentBlocks['home_hero_subtitle_size'] || 'text-xl sm:text-2xl', {'mx-auto': (contentBlocks['home_hero_align'] || 'text-center') === 'text-center'}]">
                    {{ (contentBlocks && contentBlocks['home_hero_subtitle']) ? contentBlocks['home_hero_subtitle'] : 'Experience exclusive tours, private transfers, and unforgettable adventures in paradise.' }}
                </p>
                <div class="animate-fade-in-up delay-200">
                     <Link href="/services" class="px-8 py-4 bg-primary-500 hover:bg-primary-600 text-white text-lg font-bold rounded-full shadow-xl transition transform hover:scale-105 hover:shadow-2xl flex items-center gap-2 inline-flex">
                        View All Services
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Search Widget (Floating overlap) -->
        <SearchWidget :zones="zones" />

        <!-- Featured Services & Value Props -->
        <div class="py-16 bg-gray-50 dark:bg-gray-900">
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                 <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                     <!-- Value Props (Icons) -->
                     <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md transform hover:-translate-y-1 transition duration-300" :class="[contentBlocks['home_cards_align'] || 'text-center']">
                         <div class="w-16 h-16 bg-cyan-100 text-cyan-600 rounded-full flex items-center justify-center mb-4 text-2xl overflow-hidden" :class="{'mx-auto': (contentBlocks['home_cards_align'] || 'text-center') === 'text-center'}">
                             <img v-if="contentBlocks['home_card1_image']" :src="contentBlocks['home_card1_image']" class="w-full h-full object-cover">
                             <span v-else>{{ contentBlocks['home_card1_icon'] || '✈️' }}</span>
                         </div>
                         <h3 class="font-bold text-gray-900 dark:text-white mb-2" :class="[contentBlocks['home_cards_font_family'] || 'font-sans', contentBlocks['home_cards_title_size'] || 'text-xl']">{{ contentBlocks['home_card1_title'] || 'Private Transfers' }}</h3>
                         <p class="text-gray-600 dark:text-gray-400" :class="[contentBlocks['home_cards_text_size'] || 'text-base']">{{ contentBlocks['home_card1_text'] || 'Reliable airport pickup and drop-off in luxury vehicles.' }}</p>
                     </div>
                      <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md transform hover:-translate-y-1 transition duration-300" :class="[contentBlocks['home_cards_align'] || 'text-center']">
                         <div class="w-16 h-16 bg-secondary-100 text-secondary-600 rounded-full flex items-center justify-center mb-4 text-2xl overflow-hidden" :class="{'mx-auto': (contentBlocks['home_cards_align'] || 'text-center') === 'text-center'}">
                             <img v-if="contentBlocks['home_card2_image']" :src="contentBlocks['home_card2_image']" class="w-full h-full object-cover">
                             <span v-else>{{ contentBlocks['home_card2_icon'] || '🗿' }}</span>
                         </div>
                         <h3 class="font-bold text-gray-900 dark:text-white mb-2" :class="[contentBlocks['home_cards_font_family'] || 'font-sans', contentBlocks['home_cards_title_size'] || 'text-xl']">{{ contentBlocks['home_card2_title'] || 'Exclusive Tours' }}</h3>
                         <p class="text-gray-600 dark:text-gray-400" :class="[contentBlocks['home_cards_text_size'] || 'text-base']">{{ contentBlocks['home_card2_text'] || 'Discover hidden gems and ancient ruins with expert guides.' }}</p>
                     </div>
                      <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md transform hover:-translate-y-1 transition duration-300" :class="[contentBlocks['home_cards_align'] || 'text-center']">
                         <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mb-4 text-2xl overflow-hidden" :class="{'mx-auto': (contentBlocks['home_cards_align'] || 'text-center') === 'text-center'}">
                             <img v-if="contentBlocks['home_card3_image']" :src="contentBlocks['home_card3_image']" class="w-full h-full object-cover">
                             <span v-else>{{ contentBlocks['home_card3_icon'] || '💎' }}</span>
                         </div>
                         <h3 class="font-bold text-gray-900 dark:text-white mb-2" :class="[contentBlocks['home_cards_font_family'] || 'font-sans', contentBlocks['home_cards_title_size'] || 'text-xl']">{{ contentBlocks['home_card3_title'] || 'VIP Service' }}</h3>
                         <p class="text-gray-600 dark:text-gray-400" :class="[contentBlocks['home_cards_text_size'] || 'text-base']">{{ contentBlocks['home_card3_text'] || 'Personalized attention and 24/7 support for your trip.' }}</p>
                     </div>
                 </div>

                 <!-- Featured Services List -->
                 <div v-if="featuredServices && featuredServices.length > 0">
                     <div class="mb-10" :class="[
                         contentBlocks['home_featured_align'] || 'text-center',
                         contentBlocks['home_featured_font_family'] || 'font-serif'
                     ]">
                         <h2 class="font-bold text-gray-900 dark:text-white" :class="contentBlocks['home_featured_font_size'] || 'text-3xl md:text-4xl'">
                             {{ contentBlocks['home_featured_title'] || 'Latest Packages' }}
                         </h2>
                         <p class="text-gray-600 mt-2" :class="contentBlocks['home_featured_font_size_sub'] || 'text-lg'">
                             {{ contentBlocks['home_featured_subtitle'] || 'Check out our most recent additions.' }}
                         </p>
                     </div>
                     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                         <ServiceCard v-for="service in featuredServices" :key="service.id" :service="service" />
                     </div>
                 </div>
             </div>
        </div>

        <!-- Featured Section -->
        <div class="py-20 bg-secondary-50 dark:bg-black overflow-hidden relative transition-colors duration-300">
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                    <div class="mb-12 lg:mb-0" :class="[contentBlocks['home_explore_align'] || 'text-left']">
                        <span class="text-primary-600 dark:text-primary-400 font-bold uppercase tracking-wider text-sm mb-2 block">Unforgettable Experiences</span>
                        <h2 class="font-bold text-gray-900 dark:text-white mb-6" :class="[contentBlocks['home_explore_font_family'] || 'font-serif', contentBlocks['home_explore_title_size'] || 'text-4xl']">
                            {{ (contentBlocks && contentBlocks['home_explore_title']) ? contentBlocks['home_explore_title'] : 'Explore Ancient Ruins' }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-8 leading-relaxed" :class="[contentBlocks['home_explore_text_size'] || 'text-lg']">
                            {{ (contentBlocks && contentBlocks['home_explore_text']) ? contentBlocks['home_explore_text'] : 'Immerse yourself in the history of the Mayan civilization. Visit Chichen Itza, Tulum, and Coba with our expert guides. We offer private and small group tours to ensure a personalized experience.' }}
                        </p>
                        <ul class="space-y-4 mb-8">
                             <li class="flex items-center text-gray-700 dark:text-gray-200">
                                <span class="bg-secondary-100 text-secondary-600 rounded-full p-1 mr-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                                Skip-the-line access
                            </li>
                             <li class="flex items-center text-gray-700 dark:text-gray-200">
                                <span class="bg-secondary-100 text-secondary-600 rounded-full p-1 mr-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                                Certified bilingual guides
                            </li>
                             <li class="flex items-center text-gray-700 dark:text-gray-200">
                                <span class="bg-secondary-100 text-secondary-600 rounded-full p-1 mr-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                                Refreshments included
                            </li>
                        </ul>
                        <Link href="/services" class="text-cyan-600 dark:text-cyan-400 font-bold hover:text-cyan-700 dark:hover:text-cyan-300 inline-flex items-center transition-colors duration-300">
                            View All Tours <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </Link>
                    </div>
                    <div class="mt-10 lg:mt-0 relative">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl transform hover:scale-[1.02] transition duration-500">
                            <img :src="(contentBlocks && contentBlocks['home_explore_image']) ? contentBlocks['home_explore_image'] : defaultRuins" alt="Mayan Ruins" class="w-full h-auto object-cover">
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
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                    <!-- Image (First on Mobile due to DOM order, Left on Desktop due to order class) -->
                    <div class="order-2 lg:order-1 mb-12 lg:mb-0">
                         <div class="relative rounded-2xl overflow-hidden shadow-2xl transform hover:scale-[1.02] transition duration-500">
                            <img :src="(contentBlocks && contentBlocks['home_fleet_image']) ? contentBlocks['home_fleet_image'] : defaultVan" alt="Luxury Van" class="w-full h-auto object-cover">
                        </div>
                    </div>
                    <!-- Text (Second on Mobile, Right on Desktop) -->
                    <!-- ... -->
                    <!-- Text (Second on Mobile, Right on Desktop) -->
                    <div class="order-1 lg:order-2" :class="[contentBlocks['home_fleet_align'] || 'text-left']">
                        <h2 class="font-bold text-gray-900 dark:text-white mb-6" :class="[contentBlocks['home_fleet_font_family'] || 'font-serif', contentBlocks['home_fleet_title_size'] || 'text-4xl']">
                            {{ (contentBlocks && contentBlocks['home_fleet_title']) ? contentBlocks['home_fleet_title'] : 'Travel in Luxury' }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-8" :class="[contentBlocks['home_fleet_text_size'] || 'text-lg']">
                            {{ (contentBlocks && contentBlocks['home_fleet_text']) ? contentBlocks['home_fleet_text'] : 'Our fleet consists of modern, air-conditioned vehicles including private vans and luxury SUVs. Enjoy complimentary water and a smooth ride to your destination.' }}
                        </p>
                         <ul class="space-y-4 mb-8">
                            <li v-for="(feature, idx) in (safeJsonParse(contentBlocks['home_fleet_features']) || [])" :key="idx" class="flex items-center text-secondary-700 dark:text-gray-200">
                                <span class="bg-primary-100 text-primary-600 rounded-full p-1 mr-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                                {{ feature }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blog Section -->
        <div v-if="latestPosts && latestPosts.length > 0" class="py-20 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                     <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 dark:text-white mb-4">Latest from our Blog</h2>
                     <p class="text-lg text-gray-600 dark:text-gray-300">Travel tips, guides, and updates from the Riviera Maya.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <Link v-for="post in latestPosts" :key="post.id" :href="route('blog.show', post.slug)" class="group block">
                        <div class="relative overflow-hidden rounded-lg shadow-lg aspect-video mb-4">
                            <img :src="post.image || 'https://placehold.co/600x400/2AC1D8/FFFFFF/png?text=Travel+Blog'" :alt="post.title" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500">
                             <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition"></div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-primary-600 transition">{{ post.title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 line-clamp-2">{{ post.excerpt }}</p>
                    </Link>
                </div>
                 <div class="mt-8 text-center">
                    <Link href="/blog" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-semibold">
                        Read more articles <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Reviews & TripAdvisor Section -->
        <div class="py-20 bg-gray-50 dark:bg-gray-900">
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                 <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                     
                     <!-- TripAdvisor Mock Widget -->
                     <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl flex flex-col items-center justify-center text-center border-t-4 border-[#00B67A]">
                         <img src="https://static.tacdn.com/img2/brand_refresh/Tripadvisor_lockup_horizontal_secondary_registered.svg" alt="TripAdvisor" class="h-12 mb-6 dark:invert">
                         <div class="text-5xl font-bold text-gray-900 dark:text-white mb-2">5.0</div>
                         <div class="flex text-[#00B67A] mb-4 space-x-1">
                             <svg v-for="i in 5" :key="i" class="w-8 h-8 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                         </div>
                         <p class="text-gray-500 dark:text-gray-400 mb-6">Based on <span class="font-bold text-gray-900 dark:text-white">1,250+ reviews</span></p>
                         <p class="text-sm text-gray-400">"Excellent service, on time and very professional drivers."</p>
                          <div class="mt-6 px-4 py-2 bg-[#00B67A] text-white rounded-md font-bold text-sm">Certificate of Excellence</div>
                     </div>

                     <!-- Customer Reviews List -->
                     <div class="lg:col-span-2">
                         <div class="flex justify-between items-end mb-8">
                             <div>
                                 <h2 class="text-3xl font-serif font-bold text-gray-900 dark:text-white">What our clients say</h2>
                                 <p class="text-gray-600 dark:text-gray-300 mt-2">Real feedback from verified travelers.</p>
                             </div>
                         </div>

                         <div class="grid gap-6">
                             <div v-for="review in latestReviews" :key="review.id" class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md flex gap-4">
                                 <div class="flex-shrink-0">
                                     <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xl">
                                         {{ review.reviewer_name.charAt(0) }}
                                     </div>
                                 </div>
                                 <div>
                                     <div class="flex items-center mb-2">
                                         <span class="font-bold text-gray-900 dark:text-white mr-2">{{ review.reviewer_name }}</span>
                                         <div class="flex text-yellow-400 text-sm">
                                              <span v-for="n in 5" :key="n">
                                                  <span v-if="n <= review.rating">★</span>
                                                  <span v-else class="text-gray-300">★</span>
                                              </span>
                                         </div>
                                     </div>
                                     <p class="text-gray-600 dark:text-gray-300 italic">"{{ review.content }}"</p>
                                      <p class="text-xs text-gray-400 mt-2">{{ new Date(review.created_at).toLocaleDateString() }}</p>
                                 </div>
                             </div>
                             
                             <!-- Empty State -->
                             <div v-if="!latestReviews || latestReviews.length === 0" class="text-center py-10 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300">
                                 <p class="text-gray-500">No reviews yet. Be the first to review us!</p>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
        </div>

        <!-- Partner CTA Section -->
        <div class="py-20 bg-gradient-to-r from-blue-900 to-indigo-900 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <h2 class="text-3xl md:text-4xl font-serif font-bold mb-6">Own a Travel Agency?</h2>
                <p class="text-xl text-blue-100 max-w-2xl mx-auto mb-10">
                    Launch your own digital booking platform with our white-label solution. 
                    Manage fleets, tours, and payments effortlessly.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link href="/partner/register" class="px-8 py-3 bg-white text-blue-900 font-bold rounded-full hover:bg-gray-100 transition shadow-lg">
                        Become a Partner
                    </Link>
                    <a href="#contact" class="px-8 py-3 border border-white text-white font-bold rounded-full hover:bg-white/10 transition">
                        Contact Sales
                    </a>
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

