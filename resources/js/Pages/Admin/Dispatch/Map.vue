<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    initialOrders: Array,
});

const activeOrders = ref(props.initialOrders);

// Helper to find index
const findOrderIndex = (id) => activeOrders.value.findIndex(o => o.id === id);

onMounted(() => {
    if (window.Echo) {
        window.Echo.channel('admin-map') // Using public/presence, or private?
            // "Private" channel defined in channels.php requires auth.
            // Admin is auth.
            // But wait, I defined it as `Broadcast::channel('admin-map', ...)` in channels.php.
            // This usually implies a PRIVATE channel named 'private-admin-map' in pusher terms, 
            // OR checks auth for 'admin-map'.
            // In Laravel Echo, `.private('admin-map')` is needed if it's protected by channels.php callback.
            // If I use `.channel('admin-map')`, it tries public channel.
            // Since I defined callback in channels.php, it IS private.
            .private('admin-map')
            .listen('.App\\Events\\LocationUpdated', (e) => {
                const idx = findOrderIndex(e.order_id);
                if (idx !== -1) {
                    activeOrders.value[idx].current_lat = e.lat;
                    activeOrders.value[idx].current_lng = e.lng;
                    // Update Map Marker (Placeholder logic)
                    console.log(`Updated Order ${e.order_id}: ${e.lat}, ${e.lng}`);
                }
            });
    }
});

const statusColors = {
    en_route_base: 'indigo',
    at_pickup: 'purple',
    on_board: 'pink',
    finished: 'gray',
    assigned: 'blue',
};
</script>

<template>
    <AdminLayout label="Global Map">
        <Head title="Live Map" />

        <div class="flex h-[calc(100vh-65px)]">
            <!-- Sidebar List -->
            <div class="w-1/4 bg-white border-r border-gray-200 overflow-y-auto">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold">Active Drivers ({{ activeOrders.length }})</h2>
                </div>
                <ul>
                    <li v-for="order in activeOrders" :key="order.id" class="p-4 border-b border-gray-100 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-bold text-gray-900">{{ order.driver?.name }}</div>
                                <div class="text-xs text-gray-500">{{ order.vehicle?.plate }}</div>
                            </div>
                            <Badge :color="statusColors[order.status]">{{ order.status.replace(/_/g, ' ') }}</Badge>
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            #{{ order.folio }} - {{ order.reservation_item?.service_name }}
                        </div>
                        <div class="mt-1 text-xs text-gray-400 font-mono">
                            Lat: {{ order.current_lat || 'N/A' }} | Lng: {{ order.current_lng || 'N/A' }}
                        </div>
                        <div class="mt-2 text-right">
                             <Link :href="route('admin.dispatch.index')" class="text-xs text-indigo-600 hover:text-indigo-900">
                                View Details
                             </Link>
                        </div>
                    </li>
                    <li v-if="activeOrders.length === 0" class="p-4 text-center text-gray-500 italic">
                        No active services right now.
                    </li>
                </ul>
            </div>

            <!-- Map Area -->
            <div class="flex-1 bg-gray-100 flex items-center justify-center relative">
                <div class="text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <h3 class="text-xl font-medium">Global Map Placeholder</h3>
                    <p>Integrate Google Maps / Leaflet here.</p>
                    <p class="text-sm mt-2">Listening for updates on channel: <code class="bg-gray-200 px-1 rounded">private-admin-map</code></p>
                </div>
                
                <!-- If I were implementing Leaflet, I would loop activeOrders and place markers -->
            </div>
        </div>
    </AdminLayout>
</template>
