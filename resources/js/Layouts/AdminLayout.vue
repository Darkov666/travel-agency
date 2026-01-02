<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const user = computed(() => usePage().props.auth.user);
const showingSidebar = ref(false);

const navigation = [
    { name: 'Dashboard', href: route('admin.dashboard'), routeName: 'admin.dashboard', icon: 'fas fa-home', roles: ['root', 'admin', 'supervisor', 'operator'] },
    { name: 'Organizations', href: route('admin.organizations.index'), routeName: 'admin.organizations.*', icon: 'fas fa-sitemap', roles: ['root'] },
    { name: 'Approvals', href: route('admin.change-requests.index'), routeName: 'admin.change-requests.*', icon: 'fas fa-clipboard-check', roles: ['root', 'admin'] },
    { name: 'Activity Log', href: route('admin.activity-logs.index'), routeName: 'admin.activity-logs.*', icon: 'fas fa-history', roles: ['root', 'admin'] },
    { name: 'Zones Editor', href: route('admin.zones.index'), routeName: 'admin.zones.*', icon: 'fas fa-map-marked-alt', roles: ['root', 'admin'] },
    { name: 'Providers', href: route('admin.providers.index'), routeName: 'admin.providers.*', icon: 'fas fa-building', roles: ['root', 'admin'] },
    { name: 'Service Ops', href: route('admin.reservations.index'), routeName: 'admin.reservations.*', icon: 'fas fa-tasks', roles: ['root', 'admin', 'supervisor'] },
];

const visibleNavigation = computed(() => {
    return navigation.filter(item => !item.roles || item.roles.includes(user.value.role));
});

const logout = () => {
    router.post(route('admin.logout'));
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
        <!-- Mobile Sidebar Overlay -->
        <div v-show="showingSidebar" @click="showingSidebar = false" class="fixed inset-0 z-40 bg-black opacity-50 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="{'translate-x-0': showingSidebar, '-translate-x-full': !showingSidebar}" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 text-white transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto">
            <div class="flex items-center justify-center h-16 bg-gray-900 shadow-md">
                <span class="text-xl font-bold tracking-wider text-cyan-400">TravelAdmin</span>
            </div>

            <nav class="mt-5 px-2 space-y-1">
                <Link v-for="item in visibleNavigation" :key="item.name" :href="item.href" :class="[route().current(item.routeName) ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'group flex items-center px-2 py-2 text-base font-medium rounded-md']">
                    <!-- Icon placeholder if FontAwesome not loaded, or use Heroicons -->
                    <span class="mr-3 text-lg">
                         <!-- Simple SVG Icons as fallback -->
                        <svg v-if="item.name === 'Dashboard'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <svg v-if="item.name === 'Organizations'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <svg v-if="item.name === 'Zones Editor'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        <svg v-if="item.name === 'Approvals'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <svg v-if="item.name === 'Activity Log'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <svg v-if="item.name === 'Providers'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                         <svg v-if="item.name === 'Service Ops'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </span>
                    {{ item.name }}
                </Link>
            </nav>

            <div class="absolute bottom-0 w-full p-4 bg-gray-900" v-if="user">
                <Link :href="route('admin.profile.edit')" class="flex items-center hover:bg-gray-800 p-2 rounded transition-colors group">
                    <img class="h-8 w-8 rounded-full" :src="user.profile_photo_url || 'https://ui-avatars.com/api/?name='+user.name" alt="" />
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white group-hover:text-cyan-400">{{ user.name }}</p>
                        <p class="text-xs text-gray-400">{{ user.role }}</p>
                    </div>
                </Link>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white dark:bg-gray-800 shadow flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <button @click="showingSidebar = !showingSidebar" class="text-gray-500 focus:outline-none lg:hidden">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <div class="flex-1 px-4 text-xl font-semibold text-gray-800 dark:text-white">
                    <slot name="header" />
                </div>

                <div class="flex items-center">
                     <Link href="/admin/logout" method="post" as="button" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        Logout
                    </Link>
                </div>
            </header>

            <!-- Main Scrollable Area -->
            <main class="flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-900 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
