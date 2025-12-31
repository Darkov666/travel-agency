<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { loadLanguageAsync } from 'laravel-vue-i18n';
import CartIcon from '@/Components/CartIcon.vue';
import CurrencyToggle from '@/Components/CurrencyToggle.vue';
import FloatingWhatsApp from '@/Components/FloatingWhatsApp.vue';

const showingNavigationDropdown = ref(false);
const isDark = ref(false);
const currentLang = ref('es');

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

const changeLanguage = (lang) => {
    currentLang.value = lang;
    loadLanguageAsync(lang);
    localStorage.setItem('locale', lang);
};

import { useCartStore } from '@/Stores/cart';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

onMounted(async () => {
    // Check for saved theme preference
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }

    // Check for saved language preference
    const savedLang = localStorage.getItem('locale') || 'es';
    changeLanguage(savedLang);

    // Migrate local cart to backend
    const cartStore = useCartStore();
    if (cartStore.items.length > 0) {
        try {
            for (const item of cartStore.items) {
                await axios.post(route('cart.add'), {
                    service_id: item.id,
                    quantity: 1
                });
            }
            cartStore.clearCart();
            router.reload(); // Reload to update cart count
        } catch (error) {
            console.error('Failed to migrate cart:', error);
        }
    }
});

import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import Swal from 'sweetalert2';

const page = usePage();

watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
         Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: flash.success,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
    if (flash?.error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: flash.error,
            confirmButtonColor: '#E99FA0',
        });
    }
}, { deep: true });

</script>

<template>
    <div class="min-h-screen bg-primary-50 dark:bg-secondary-950 font-sans text-secondary-700 dark:text-secondary-200 transition-colors duration-300">
        <!-- Navigation -->
        <nav class="bg-cyan-600 dark:bg-gray-900 border-b border-cyan-500 dark:border-gray-800 sticky top-0 z-50 transition-colors duration-300 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link href="/" class="text-2xl font-serif font-bold text-white transition hover:text-cyan-50">
                                Cancun Sunny
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-6 sm:-my-px sm:ml-10 sm:flex">
                           <Link href="/" :class="{'border-white text-white dark:border-cyan-400 dark:text-white': $page.url === '/', 'border-transparent text-cyan-50 dark:text-gray-300 hover:text-white dark:hover:text-white hover:border-cyan-200': $page.url !== '/'}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold leading-5 transition-colors duration-300 ease-in-out">
                                {{ $t('nav.home') }}
                            </Link> 
                            <Link href="/services" :class="{'border-white text-white dark:border-cyan-400 dark:text-white': $page.url.startsWith('/services'), 'border-transparent text-cyan-50 dark:text-gray-300 hover:text-white dark:hover:text-white hover:border-cyan-200': !$page.url.startsWith('/services')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold leading-5 transition-colors duration-300 ease-in-out">
                                {{ $t('nav.services') }}
                            </Link>
                            <Link href="/shop" :class="{'border-white text-white dark:border-cyan-400 dark:text-white': $page.url.startsWith('/shop'), 'border-transparent text-cyan-50 dark:text-gray-300 hover:text-white dark:hover:text-white hover:border-cyan-200': !$page.url.startsWith('/shop')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold leading-5 transition-colors duration-300 ease-in-out">
                                {{ $t('nav.shop') }}
                            </Link>
                            <Link href="/blog" :class="{'border-white text-white dark:border-cyan-400 dark:text-white': $page.url.startsWith('/blog'), 'border-transparent text-cyan-50 dark:text-gray-300 hover:text-white dark:hover:text-white hover:border-cyan-200': !$page.url.startsWith('/blog')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold leading-5 transition-colors duration-300 ease-in-out">
                                {{ $t('nav.blog') }}
                            </Link>
                            <Link href="/contact" :class="{'border-white text-white dark:border-cyan-400 dark:text-white': $page.url.startsWith('/contact'), 'border-transparent text-cyan-50 dark:text-gray-300 hover:text-white dark:hover:text-white hover:border-cyan-200': !$page.url.startsWith('/contact')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold leading-5 transition-colors duration-300 ease-in-out">
                                {{ $t('nav.contact') }}
                            </Link>
                        </div>
                    </div>

                    <!-- Right Side Toggles -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-3">
                        <!-- Currency Toggle -->
                        <CurrencyToggle />

                        <!-- Cart Icon -->
                        <CartIcon />

                        <!-- Language Switcher -->
                        <div class="flex items-center space-x-2 text-sm border-l border-primary-400 dark:border-gray-600 pl-3">
                            <button @click="changeLanguage('es')" :class="{'font-bold text-white dark:text-white': currentLang === 'es', 'text-primary-200 dark:text-gray-400': currentLang !== 'es'}" class="hover:text-white transition">ES</button>
                            <span class="text-primary-300 dark:text-gray-500">|</span>
                            <button @click="changeLanguage('en')" :class="{'font-bold text-white dark:text-white': currentLang === 'en', 'text-primary-200 dark:text-gray-400': currentLang !== 'en'}" class="hover:text-white transition">EN</button>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button @click="toggleDarkMode" class="p-2 rounded-full text-primary-100 hover:bg-primary-500 dark:text-gray-200 dark:hover:bg-gray-700 transition focus:outline-none">
                            <svg v-if="!isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>

                        <!-- Login Icon -->
                        <Link href="/admin/login" class="p-2 rounded-full text-primary-100 hover:bg-primary-500 dark:text-gray-200 dark:hover:bg-gray-700 transition focus:outline-none" :title="$t('nav.login')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </Link>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-primary-100 hover:text-white hover:bg-primary-500 focus:outline-none focus:bg-primary-500 focus:text-white transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown}" class="sm:hidden bg-white dark:bg-secondary-900 border-b border-secondary-200 dark:border-secondary-800">
                <div class="pt-2 pb-3 space-y-1">
                    <Link href="/" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-secondary-600 dark:text-secondary-400 hover:text-secondary-800 dark:hover:text-white hover:bg-primary-50 dark:hover:bg-secondary-800 hover:border-primary-300 transition duration-150 ease-in-out">{{ $t('nav.home') }}</Link>
                    <Link href="/services" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-secondary-600 dark:text-secondary-400 hover:text-secondary-800 dark:hover:text-white hover:bg-primary-50 dark:hover:bg-secondary-800 hover:border-primary-300 transition duration-150 ease-in-out">{{ $t('nav.services') }}</Link>
                    <Link href="/shop" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-secondary-600 dark:text-secondary-400 hover:text-secondary-800 dark:hover:text-white hover:bg-primary-50 dark:hover:bg-secondary-800 hover:border-primary-300 transition duration-150 ease-in-out">{{ $t('nav.shop') }}</Link>
                    <Link href="/blog" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-secondary-600 dark:text-secondary-400 hover:text-secondary-800 dark:hover:text-white hover:bg-primary-50 dark:hover:bg-secondary-800 hover:border-primary-300 transition duration-150 ease-in-out">{{ $t('nav.blog') }}</Link>
                    <Link href="/contact" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-secondary-600 dark:text-secondary-400 hover:text-secondary-800 dark:hover:text-white hover:bg-primary-50 dark:hover:bg-secondary-800 hover:border-primary-300 transition duration-150 ease-in-out">{{ $t('nav.contact') }}</Link>
                    <Link href="/admin/login" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-secondary-600 dark:text-secondary-400 hover:text-secondary-800 dark:hover:text-white hover:bg-primary-50 dark:hover:bg-secondary-800 hover:border-primary-300 transition duration-150 ease-in-out">{{ $t('nav.login') }}</Link>
                </div>
                <!-- Mobile Toggles -->
                <div class="pt-4 pb-4 border-t border-secondary-200 dark:border-secondary-800 flex items-center justify-between px-4">
                     <div class="flex items-center space-x-4">
                        <button @click="changeLanguage('es')" :class="{'font-bold text-primary-600': currentLang === 'es'}" class="text-sm text-secondary-600 dark:text-secondary-400">ES</button>
                        <button @click="changeLanguage('en')" :class="{'font-bold text-primary-600': currentLang === 'en'}" class="text-sm text-secondary-600 dark:text-secondary-400">EN</button>
                    </div>
                    <button @click="toggleDarkMode" class="text-secondary-500 dark:text-secondary-400">
                        <span v-if="!isDark">Dark Mode</span>
                        <span v-else>Light Mode</span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>

        <FloatingWhatsApp />

        <!-- Footer -->
        <footer class="bg-secondary-50 dark:bg-black text-secondary-600 dark:text-gray-400 border-t border-secondary-200 dark:border-secondary-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="md:col-span-1">
                        <h3 class="text-xl font-serif font-bold mb-4 text-secondary-900 dark:text-white transition-colors duration-300">Cancun Sunny</h3>
                        <p class="text-sm leading-relaxed transition-colors duration-300">
                            {{ $t('footer.description') }}
                        </p>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-secondary-900 dark:text-white transition-colors duration-300">{{ $t('footer.quick_links') }}</h4>
                        <ul class="space-y-2 text-sm transition-colors duration-300">
                            <li><Link href="/services" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-300">{{ $t('nav.services') }}</Link></li>
                            <li><Link href="/blog" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-300">{{ $t('nav.blog') }}</Link></li>
                            <li><Link href="/contact" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-300">{{ $t('nav.contact') }}</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-secondary-900 dark:text-white transition-colors duration-300">{{ $t('footer.contact') }}</h4>
                        <ul class="space-y-2 text-sm transition-colors duration-300">
                            <li>123 Wellness Blvd</li>
                            <li>Cancun, Quintana Roo</li>
                            <li>contact@cancunsunny.com</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-secondary-900 dark:text-white transition-colors duration-300">{{ $t('footer.social') }}</h4>
                        <div class="flex space-x-4">
                            <!-- Social Icons... -->
                        </div>
                    </div>                    
                </div>
                <div class="mt-8 pt-8 border-t border-secondary-200 dark:border-secondary-800 text-center text-sm text-secondary-500 dark:text-gray-500 transition-colors duration-300">
                    &copy; {{ new Date().getFullYear() }} Cancun Sunny. {{ $t('footer.rights') }}
                </div>
            </div>
        </footer>
    </div>
</template>
