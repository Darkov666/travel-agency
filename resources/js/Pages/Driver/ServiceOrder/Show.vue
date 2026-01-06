<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Badge from '@/Components/Badge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { format } from 'date-fns';

const props = defineProps({
    order: Object,
    is_driver: Boolean
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

const page = usePage();
const flashSuccess = computed(() => page.props.flash.success);
const flashError = computed(() => page.props.flash.error);

const acceptOrder = () => {
    router.post(route('driver.order.accept', props.order.id));
};

const rejectOrder = () => {
    if (confirm('Are you sure you want to REJECT this assignment?')) {
        router.post(route('driver.order.reject', props.order.id));
    }
};

const updateStatus = (status) => {
    if (confirm(`Update status to: ${status.replace(/_/g, ' ').toUpperCase()}?`)) {
        if (navigator.geolocation && ['en_route_base', 'at_pickup', 'on_board', 'finished'].includes(status)) {
            navigator.geolocation.getCurrentPosition((position) => {
                router.post(route('driver.order.status', props.order.id), {
                    status: status,
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                });
            }, (error) => {
                console.error("GPS Error", error);
                // Fail gracefully if GPS denied, still send status
                router.post(route('driver.order.status', props.order.id), { status: status });
            });
        } else {
             router.post(route('driver.order.status', props.order.id), { status: status });
        }
    }
};

const canAction = computed(() => props.is_driver && !['finished', 'cancelled', 'rejected'].includes(props.order.status));
</script>

<template>
    <AdminLayout :label="`Service #${order.folio}`">
        <Head title="Service Details" />

        <div class="py-6 px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Notifications -->
            <div v-if="flashSuccess" class="bg-green-100 text-green-800 p-4 rounded mb-4">
                {{ flashSuccess }}
            </div>
            <div v-if="flashError" class="bg-red-100 text-red-800 p-4 rounded mb-4">
                {{ flashError }}
            </div>

            <!-- Status Header -->
            <div class="bg-white shadow rounded-lg p-6 flex flex-col items-center justify-center space-y-2">
                 <div class="text-sm text-gray-500 uppercase tracking-wide">Current Status</div>
                 <Badge :color="statusColors[order.status]" class="text-lg px-4 py-1">
                     {{ order.status.replace(/_/g, ' ') }}
                 </Badge>
            </div>

            <!-- Action Area (Driver Only) -->
            <div v-if="canAction" class="bg-white shadow rounded-lg p-6">
                <!-- Acceptance Flow -->
                <div v-if="order.status === 'assigned'" class="flex gap-4">
                    <PrimaryButton class="w-full justify-center py-4 text-lg bg-green-600 hover:bg-green-700" @click="acceptOrder">
                        ACCEPT ORDER
                    </PrimaryButton>
                    <DangerButton class="w-full justify-center py-4 text-lg" @click="rejectOrder">
                        REJECT
                    </DangerButton>
                </div>

                <!-- Execution Flow -->
                <div v-else-if="['accepted', 'en_route_base', 'at_pickup', 'on_board'].includes(order.status)" class="space-y-4">
                    <p class="text-center text-sm text-gray-600">Update Service Checkpoints</p>
                    
                    <button v-if="order.status === 'accepted'" 
                            @click="updateStatus('en_route_base')"
                            class="w-full bg-indigo-600 text-white rounded-lg py-4 font-bold text-xl shadow hover:bg-indigo-700 transition">
                        START SHIFT (En Camino)
                    </button>

                    <button v-if="order.status === 'en_route_base'"
                            @click="updateStatus('at_pickup')"
                            class="w-full bg-purple-600 text-white rounded-lg py-4 font-bold text-xl shadow hover:bg-purple-700 transition">
                        ARRIVED AT PICKUP
                    </button>

                    <button v-if="order.status === 'at_pickup'"
                            @click="updateStatus('on_board')"
                            class="w-full bg-pink-600 text-white rounded-lg py-4 font-bold text-xl shadow hover:bg-pink-700 transition">
                        PAX ON BOARD (Start Trip)
                    </button>

                    <button v-if="order.status === 'on_board'"
                            @click="updateStatus('finished')"
                            class="w-full bg-gray-800 text-white rounded-lg py-4 font-bold text-xl shadow hover:bg-gray-900 transition">
                        FINISH TRIP
                    </button>
                </div>
            </div>

            <div v-else-if="props.is_driver && order.status === 'rejected'" class="bg-red-50 border border-red-200 text-red-700 p-4 rounded text-center">
                You have rejected this order.
            </div>

            <!-- Details Card -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Trip Details</h3>
                </div>
                <div class="px-4 py-5 sm:p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Date</span>
                            <div class="font-medium">{{ order.reservation_item?.date ? format(new Date(order.reservation_item.date), 'MMM dd, yyyy') : 'N/A' }}</div>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Time</span>
                            <div class="font-medium">{{ order.reservation_item?.pickup_time }}</div>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-gray-500 uppercase">Service</span>
                        <div class="font-medium">{{ order.reservation_item?.service_name }}</div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                         <div class="flex items-start">
                             <div class="flex-shrink-0 h-5 w-5 rounded-full bg-green-100 flex items-center justify-center text-green-600 mt-0.5">A</div>
                             <div class="ml-3">
                                 <p class="text-sm font-medium text-gray-900">Pickup</p>
                                 <p class="text-sm text-gray-500">{{ order.reservation_item?.pickup_location }}</p>
                             </div>
                         </div>
                         <div class="flex items-start mt-4">
                             <div class="flex-shrink-0 h-5 w-5 rounded-full bg-red-100 flex items-center justify-center text-red-600 mt-0.5">B</div>
                             <div class="ml-3">
                                 <p class="text-sm font-medium text-gray-900">Dropoff</p>
                                 <p class="text-sm text-gray-500">{{ order.reservation_item?.dropoff_location }}</p>
                             </div>
                         </div>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4 grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs text-gray-500 uppercase">Pax</span>
                            <div class="font-medium">{{ order.reservation_item?.pax }} People</div>
                        </div>
                         <div>
                            <span class="text-xs text-gray-500 uppercase">Flight</span>
                            <div class="font-medium">{{ order.reservation_item?.airline }} {{ order.reservation_item?.flight_number }}</div>
                        </div>
                    </div>

                     <div v-if="order.comments" class="border-t border-gray-100 pt-4">
                        <span class="text-xs text-gray-500 uppercase">Notes</span>
                        <p class="text-sm text-gray-700 italic">{{ order.comments }}</p>
                    </div>
                </div>
            </div>

            <!-- Checkpoint Logs -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Activity Log</h3>
                </div>
                <div class="px-4 py-5 sm:p-6 text-sm text-gray-600 space-y-2">
                    <div v-for="(time, key) in order.checkpoints" :key="key" class="flex justify-between">
                        <span class="capitalize">{{ key.replace(/_/g, ' ').replace('at', '') }}</span>
                        <span class="font-mono text-gray-500">{{ time }}</span>
                    </div>
                    <div v-if="!order.checkpoints || Object.keys(order.checkpoints).length === 0" class="text-center italic text-gray-400">
                        No activity recorded yet.
                    </div>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>
