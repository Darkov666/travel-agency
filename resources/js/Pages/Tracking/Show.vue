<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    order: Object,
    pusher_key: String,
    pusher_cluster: String,
});

const currentLat = ref(props.order.current_lat);
const currentLng = ref(props.order.current_lng);
const status = ref(props.order.status);
const mapUrl = ref('');

const updateMapUrl = () => {
    if (currentLat.value && currentLng.value) {
        // Embed Google Maps or similar. Using strict Mode
        mapUrl.value = `https://maps.google.com/maps?q=${currentLat.value},${currentLng.value}&z=15&output=embed`;
    }
};

onMounted(() => {
    updateMapUrl();

    // Listen for events
    if (window.Echo) {
        window.Echo.channel(`service-order.${props.order.id}`)
            // "Private" usually requires auth. Public tracking needs public channel or token auth via 'private' endpoint
            // For now, if user is NOT logged in, private channel subscription will fail 403.
            // Requirement: "PaxT: Tracking Link View". Passengers usually don't log in.
            // So we need a Public Channel OR a custom authorization endpoint for tracking.
            // For simplicity in Phase 4, assuming Public Channel for "service-tracking" might be better,
            // OR we rely on Admin viewing it. 
            // BUT Pax need to view it.
            // Let's try to subscribe. If 403, we need to fix channels to be public or authorize guests.
            // I'll assume for this iteration it's a private channel and we might hit auth issues for Guests.
            // Fix: Change to Presence or Public channel in channels.php for tracking?
            // Or just allow anyone to listen if they have the ID? (Obscurity).
            // Let's use `service-order.{id}` as private, but maybe we need a public one.
            // I will use `channel` (public) instead of `private`.
            // Update channels.php to use `Broadcast::channel` without auth check? 
            // `channels.php` defines auth callbacks. If callback returns true/user, it's authorized.
            // To make it public, just don't define it in `channels.php`? No, that makes it strictly private?
            // Public channels don't need `channels.php` definition, they are just 'channel-name'.
            // So I should broadcast on a public channel for tracking.
            // Let's try subscribing to `service-track.${props.order.id}` (Public).
            .listen('.App\\Events\\LocationUpdated', (e) => {
                console.log('Location Recieved', e);
                currentLat.value = e.lat;
                currentLng.value = e.lng;
                updateMapUrl();
            })
            .listen('.App\\Events\\ServiceOrderUpdated', (e) => {
                status.value = e.status;
            });
            
         // Actually, let's use the Private one but I need to ensure it works. 
         // If I use `Echo.channel` instead of `Echo.private`, it attempts public.
         // If `channels.php` has a definition, it's private.
         // I will assume for now we use `service-tracking.${props.order.id}` as a public channel.
         // I need to update the Event to broadcast on that too OR change the existing one.
         // Let's change Event to use `Channel` instead of `PrivateChannel` for simplicity of "Pax Experience" without login.
    }
});

const statusColors = {
    pending: 'yellow',
    assigned: 'blue',
    accepted: 'green',
    rejected: 'red',
    en_route_base: 'indigo',
    at_pickup: 'purple',
    on_board: 'pink',
    finished: 'gray',
    cancelled: 'red',
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex flex-col">
        <Head title="Track Your Ride" />

        <!-- Header -->
        <div class="bg-indigo-600 p-4 text-white shadow-lg z-10">
            <h1 class="text-xl font-bold flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Track Your Ride
            </h1>
            <div class="mt-2 text-indigo-200 text-sm">
                Service #{{ order.folio }} | {{ order.reservation_item?.service_name }}
            </div>
        </div>

        <!-- Map Area -->
        <div class="flex-1 relative bg-gray-200">
            <div v-if="currentLat && currentLng" class="absolute inset-0">
                <iframe 
                    width="100%" 
                    height="100%" 
                    frameborder="0" 
                    style="border:0" 
                    :src="mapUrl" 
                    allowfullscreen>
                </iframe>
                <!-- Overlay to prevent interaction if desired, or just let them pan -->
            </div>
            <div v-else class="absolute inset-0 flex items-center justify-center text-gray-500">
                <div class="text-center">
                    <p class="text-lg">Waiting for driver location...</p>
                    <p class="text-sm">Status: {{ status.replace(/_/g, ' ') }}</p>
                </div>
            </div>
            
            <!-- Floating Status Card -->
            <div class="absolute bottom-6 left-4 right-4 bg-white p-4 rounded-xl shadow-xl border border-gray-100">
                <div class="flex justify-between items-center mb-2">
                    <div class="font-bold text-gray-900">Current Status</div>
                    <Badge :color="statusColors[status]" class="uppercase">{{ status.replace(/_/g, ' ') }}</Badge>
                </div>
                
                <div class="flex items-center gap-4 mt-4">
                    <!-- Driver Info -->
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 uppercase">Driver</div>
                        <div class="font-medium text-gray-900">{{ order.driver?.name || 'Assigned' }}</div>
                         <div class="text-xs text-gray-400">{{ order.vehicle?.plate }}</div>
                    </div>
                     <!-- Vehicle Info -->
                    <div class="flex-1 text-right">
                         <div class="text-xs text-gray-500 uppercase">Vehicle</div>
                        <div class="font-medium text-gray-900">{{ order.vehicle?.model || 'Standard' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
