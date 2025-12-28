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
        <nav class="bg-white dark:bg-secondary-900 border-b border-primary-100 dark:border-secondary-800 sticky top-0 z-50 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link href="/" class="text-2xl font-serif font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition">
                                Conecta Contigo
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-6 sm:-my-px sm:ml-10 sm:flex">
                            <Link href="/" :class="{'border-primary-500 text-secondary-900 dark:text-white': $page.url === '/', 'border-transparent text-secondary-500 dark:text-secondary-400 hover:text-secondary-700 dark:hover:text-secondary-200 hover:border-secondary-300': $page.url !== '/'}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                {{ $t('nav.home') }}
                            </Link>
                            <Link href="/services" :class="{'border-primary-500 text-secondary-900 dark:text-white': $page.url.startsWith('/services'), 'border-transparent text-secondary-500 dark:text-secondary-400 hover:text-secondary-700 dark:hover:text-secondary-200 hover:border-secondary-300': !$page.url.startsWith('/services')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                {{ $t('nav.services') }}
                            </Link>
                            <Link href="/shop" :class="{'border-primary-500 text-secondary-900 dark:text-white': $page.url.startsWith('/shop'), 'border-transparent text-secondary-500 dark:text-secondary-400 hover:text-secondary-700 dark:hover:text-secondary-200 hover:border-secondary-300': !$page.url.startsWith('/shop')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                {{ $t('nav.shop') }}
                            </Link>
                            <Link href="/blog" :class="{'border-primary-500 text-secondary-900 dark:text-white': $page.url.startsWith('/blog'), 'border-transparent text-secondary-500 dark:text-secondary-400 hover:text-secondary-700 dark:hover:text-secondary-200 hover:border-secondary-300': !$page.url.startsWith('/blog')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                {{ $t('nav.blog') }}
                            </Link>
                            <Link href="/contact" :class="{'border-primary-500 text-secondary-900 dark:text-white': $page.url.startsWith('/contact'), 'border-transparent text-secondary-500 dark:text-secondary-400 hover:text-secondary-700 dark:hover:text-secondary-200 hover:border-secondary-300': !$page.url.startsWith('/contact')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out">
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
                        <div class="flex items-center space-x-2 text-sm border-l border-secondary-200 dark:border-secondary-700 pl-3">
                            <button @click="changeLanguage('es')" :class="{'font-bold text-primary-600 dark:text-primary-400': currentLang === 'es', 'text-secondary-500 dark:text-secondary-400': currentLang !== 'es'}" class="hover:text-primary-500 transition">ES</button>
                            <span class="text-secondary-300">|</span>
                            <button @click="changeLanguage('en')" :class="{'font-bold text-primary-600 dark:text-primary-400': currentLang === 'en', 'text-secondary-500 dark:text-secondary-400': currentLang !== 'en'}" class="hover:text-primary-500 transition">EN</button>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button @click="toggleDarkMode" class="p-2 rounded-full text-secondary-500 hover:bg-primary-50 dark:hover:bg-secondary-800 transition focus:outline-none">
                            <svg v-if="!isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </button>

                        <!-- Register Link -->
                        <Link href="/register" class="text-sm font-bold text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 transition hidden lg:block">
                            {{ $t('nav.register') }}
                        </Link>

                        <!-- Login Icon -->
                        <Link href="/login" class="p-2 rounded-full text-secondary-500 hover:bg-primary-50 dark:hover:bg-secondary-800 transition focus:outline-none" :title="$t('nav.login')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </Link>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-secondary-400 hover:text-secondary-500 hover:bg-secondary-100 focus:outline-none focus:bg-secondary-100 focus:text-secondary-500 transition duration-150 ease-in-out">
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
                    <Link href="/register" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-secondary-600 dark:text-secondary-400 hover:text-secondary-800 dark:hover:text-white hover:bg-primary-50 dark:hover:bg-secondary-800 hover:border-primary-300 transition duration-150 ease-in-out">{{ $t('nav.register') }}</Link>
                    <Link href="/login" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-secondary-600 dark:text-secondary-400 hover:text-secondary-800 dark:hover:text-white hover:bg-primary-50 dark:hover:bg-secondary-800 hover:border-primary-300 transition duration-150 ease-in-out">{{ $t('nav.login') }}</Link>
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
        <footer class="bg-secondary-900 dark:bg-black text-secondary-100 border-t border-secondary-800">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="md:col-span-1">
                        <h3 class="text-xl font-serif font-bold mb-4 text-white">TherapyApp</h3>
                        <p class="text-secondary-400 text-sm leading-relaxed">
                            {{ $t('footer.description') }}
                        </p>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">{{ $t('footer.quick_links') }}</h4>
                        <ul class="space-y-2 text-sm">
                            <li><Link href="/services" class="hover:text-primary-400 transition">{{ $t('nav.services') }}</Link></li>
                            <li><Link href="/blog" class="hover:text-primary-400 transition">{{ $t('nav.blog') }}</Link></li>
                            <li><Link href="/contact" class="hover:text-primary-400 transition">{{ $t('nav.contact') }}</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">{{ $t('footer.contact') }}</h4>
                        <ul class="space-y-2 text-sm text-secondary-400">
                            <li>123 Wellness Blvd</li>
                            <li>City, State 12345</li>
                            <li>contact@therapyapp.com</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">{{ $t('footer.social') }}</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="text-secondary-400 hover:text-primary-400 transition">
                                <span class="sr-only">Facebook</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="text-secondary-400 hover:text-primary-400 transition">
                                <span class="sr-only">Instagram</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465C9.673 2.013 10.03 2 12.48 2h.165zm-1.99 1.99c-2.444.01-2.753.023-3.61.063-.853.039-1.316.173-1.624.293-.406.158-.695.346-.998.649-.303.303-.491.592-.649.998-.12.308-.254.771-.293 1.624-.04.857-.053 1.166-.063 3.61v.2c.01 2.444.023 2.753.063 3.61.039.853.173 1.316.293 1.624.158.406.346.695.649.998.303.303.592.491.998.649.308.12.771.254 1.624.293.857.04 1.166.053 3.61.063h.2c2.444-.01 2.753-.023 3.61-.063.853-.039 1.316-.173 1.624-.293.406-.158.695-.346.998-.649.303-.303.491-.592.649-.998.12-.308.254-.771.293-1.624.04-.857.053-1.166.063-3.61v-.2c-.01-2.444-.023-2.753-.063-3.61-.039-.853-.173-1.316-.293-1.624-.158-.406-.346-.695-.649-.998-.303-.303-.592-.491-.998-.649-.308-.12-.771-.254-1.624-.293-.857-.04-1.166-.053-3.61-.063h-.2zm-3.18 3.06a1.44 1.44 0 110 2.88 1.44 1.44 0 010-2.88zm5.17 1.12c-2.761 0-5 2.239-5 5s2.239 5 5 5 5-2.239 5-5-2.239-5-5-5zm0 2c1.657 0 3 1.343 3 3s-1.343 3-3 3-3-1.343-3-3 1.343-3 3-3z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="text-secondary-400 hover:text-primary-400 transition">
                                <span class="sr-only">TikTok</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93v6.16c0 3.18-1.32 4.89-3.92 5.35-2.4.43-4.93-.5-6.07-2.65-1.18-2.23-.51-4.96 1.56-6.18 1.25-.74 2.69-.8 4.12-.25v4.48c-.25-.03-.5-.05-.75-.02-.85.09-1.51.71-1.62 1.55-.16 1.21 1.16 1.96 2.15 1.4 1.05-.6 1.35-1.84 1.32-3.07V4.6a7.18 7.18 0 003.13-4.58z"/>
                                </svg>
                            </a>
                        </div>
                    </div>                    
                </div>
                <div class="mt-8 pt-8 border-t border-secondary-800 text-center text-sm text-secondary-500">
                    &copy; {{ new Date().getFullYear() }} TherapyApp. {{ $t('footer.rights') }}
                </div>
            </div>
        </footer>
    </div>
</template>
