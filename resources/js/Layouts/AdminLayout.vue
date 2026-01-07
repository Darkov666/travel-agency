<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const user = computed(() => usePage().props.auth.user);
const showingSidebar = ref(false);
const showingNotifications = ref(false);

const navigation = [
    { name: 'Dashboard', label: 'admin_nav.dashboard', routeTarget: 'admin.dashboard', routeName: 'admin.dashboard', icon: 'fas fa-home', roles: ['root', 'admin', 'supervisor', 'operator'] },
    { name: 'Content', label: 'admin_nav.content', routeTarget: 'admin.content.index', routeName: 'admin.content.*', icon: 'fas fa-pen-fancy', roles: ['root', 'admin'] },
    { name: 'Blog', label: 'admin_nav.blog', routeTarget: 'admin.blog.index', routeName: 'admin.blog.*', icon: 'fas fa-newspaper', roles: ['root', 'admin'] },
    { name: 'Blog Categories', label: 'admin_nav.blog_categories', routeTarget: 'admin.blog-topics.index', routeName: 'admin.blog-topics.*', icon: 'fas fa-tags', roles: ['root', 'admin'] },
    { name: 'Organizations', label: 'admin_nav.organizations', routeTarget: 'admin.organizations.index', routeName: 'admin.organizations.*', icon: 'fas fa-sitemap', roles: ['root'] },
    { name: 'Approvals', label: 'admin_nav.approvals', routeTarget: 'admin.change-requests.index', routeName: 'admin.change-requests.*', icon: 'fas fa-clipboard-check', roles: ['root', 'admin'] },
    { name: 'Activity Log', label: 'admin_nav.activity_log', routeTarget: 'admin.activity-logs.index', routeName: 'admin.activity-logs.*', icon: 'fas fa-history', roles: ['root', 'admin'] },
    { name: 'Zones Editor', label: 'admin_nav.zones', routeTarget: 'admin.zones.index', routeName: 'admin.zones.*', icon: 'fas fa-map-marked-alt', roles: ['root', 'admin'] },
    { name: 'Service Catalog', label: 'admin_nav.services', routeTarget: 'admin.services.index', routeName: 'admin.services.*', icon: 'fas fa-umbrella-beach', roles: ['root', 'admin'] },
    { name: 'Providers', label: 'admin_nav.providers', routeTarget: 'admin.providers.index', routeName: 'admin.providers.*', icon: 'fas fa-building', roles: ['root', 'admin'] },
    { name: 'Service Ops', label: 'admin_nav.service_ops', routeTarget: 'admin.reservations.index', routeName: 'admin.reservations.*', icon: 'fas fa-tasks', roles: ['root', 'admin', 'supervisor'] },
    { name: 'Feedback & Reviews', label: 'admin_nav.feedback', routeTarget: 'admin.feedback.index', routeName: 'admin.feedback.*', icon: 'fas fa-comments', roles: ['root', 'admin'] },
    { name: 'Comments', label: 'admin_nav.comments', routeTarget: 'admin.comments.index', routeName: 'admin.comments.*', icon: 'fas fa-comment-dots', roles: ['root', 'admin'] },
    // Provider Links
    { name: 'My Zones', label: 'admin_nav.my_zones', routeTarget: 'provider.zones.index', routeName: 'provider.zones.*', icon: 'fas fa-map-marked-alt', roles: ['provider', 'vendor'] },
];

const visibleNavigation = computed(() => {
    if (!user.value) return [];
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
                <Link v-for="item in visibleNavigation" :key="item.name" :href="route(item.routeTarget)" :class="[route().current(item.routeName) ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 'group flex items-center px-2 py-2 text-base font-medium rounded-md']">
                    <!-- Icon placeholder if FontAwesome not loaded, or use Heroicons -->
                    <span class="mr-3 text-lg">
                         <!-- Simple SVG Icons as fallback -->
                        <svg v-if="item.name === 'Dashboard'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <svg v-if="item.name === 'Organizations'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <svg v-if="item.name === 'Zones Editor'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        <svg v-if="item.name === 'Service Catalog'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                        <svg v-if="item.name === 'Approvals'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2-2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <svg v-if="item.name === 'Activity Log'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <svg v-if="item.name === 'Blog Categories'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        <svg v-if="item.name === 'Providers'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <svg v-if="item.name === 'Service Ops'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <svg v-if="item.name === 'Feedback & Reviews'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        <svg v-if="item.name === 'Comments'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                    </span>
                    {{ $t(item.label) }}
                    <span v-if="item.name === 'Comments' && $page.props.auth.user && $page.props.auth.user.unread_notifications_count > 0" 
                        class="ml-auto inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
                        {{ $page.props.auth.user.unread_notifications_count }}
                    </span>
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

                <div class="flex items-center space-x-4">
                    <!-- Notifications Dropdown -->
                    <div class="relative ml-3">
                        <button @click="showingNotifications = !showingNotifications" class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 relative">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span v-if="$page.props.auth.user && $page.props.auth.user.unread_notifications_count > 0" class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-500"></span>
                        </button>

                        <div v-if="showingNotifications" 
                             class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                            <div v-if="$page.props.auth.notifications && $page.props.auth.notifications.length > 0">
                                    <Link v-for="notification in $page.props.auth.notifications" :key="notification.id"
                                          :href="notification.data.action_url"
                                          class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors">
                                        <p class="text-sm text-gray-900" 
                                           :class="{'font-bold': !notification.read_at, 'font-normal': notification.read_at}">
                                            {{ notification.data.message }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">{{ new Date(notification.created_at).toLocaleString() }}</p>
                                    </Link>
                            </div>
                            <div v-else class="px-4 py-3 text-sm text-gray-500 text-center">
                                No new notifications
                            </div>
                        </div>
                    </div>

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
