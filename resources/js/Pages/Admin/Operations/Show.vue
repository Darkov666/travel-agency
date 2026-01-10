<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Badge from '@/Components/Badge.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { format } from 'date-fns';

const props = defineProps({
    order: Object,
    drivers: Array,
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

// Checkpoints
const checkpoints = computed(() => {
    if (!props.order.checkpoints) return [];
    // Format: { 'assigned_at': timestamp, ... }
    return Object.entries(props.order.checkpoints)
        .map(([key, timestamp]) => ({
            key: key.replace('_at', '').replace('_', ' ').toUpperCase(),
            timestamp: format(new Date(timestamp), 'MMM dd, HH:mm')
        }))
        .sort((a, b) => new Date(props.order.checkpoints[a.key]) - new Date(props.order.checkpoints[b.key])); // Rough sort, specific key order better
});

// Modals
const showAssignModal = ref(false);
const showEditStatusModal = ref(false);
const assignForm = ref({ driver_id: props.order.driver_id || '', vehicle_id: props.order.vehicle_id || '' });
const statusForm = ref({ status: props.order.status, comments: props.order.comments || '' });

const updateAssignment = () => {
     router.post(route('admin.dispatch.assign', props.order.id), assignForm.value, {
        onSuccess: () => showAssignModal.value = false
    });
};

const updateStatus = () => {
    router.put(route('admin.operations.update', props.order.id), statusForm.value, {
        onSuccess: () => showEditStatusModal.value = false
    });
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};
</script>

<template>
    <AdminLayout :label="'Order #' + order.folio">
        <Head title="Service Detail" />

        <div class="py-6 px-4 max-w-7xl mx-auto">
            <!-- Header -->
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                        Order #{{ order.folio }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Reference: {{ order.reservation_item?.reservation?.booking_ref }}
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                    <button @click="showEditStatusModal = true" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                        Edit Status
                    </button>
                    <PrimaryButton @click="showAssignModal = true">
                        {{ order.driver_id ? 'Reassign Driver' : 'Assign Driver' }}
                    </PrimaryButton>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Column: Details -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Service Info -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Service Details</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6 grid grid-cols-1 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Service Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white uppercase">{{ order.reservation_item?.service?.type }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Service Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ order.reservation_item?.service_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ order.reservation_item?.date ? format(new Date(order.reservation_item.date), 'MMM dd, yyyy') : 'N/A' }} 
                                    at {{ order.reservation_item?.pickup_time }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Pax</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ order.reservation_item?.adults }} Adults, {{ order.reservation_item?.children }} Children
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Route</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white flex items-center">
                                    <span class="font-semibold">{{ order.reservation_item?.pickup_location }}</span>
                                    <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                    <span class="font-semibold">{{ order.reservation_item?.dropoff_location }}</span>
                                </dd>
                            </div>
                            <div class="sm:col-span-2" v-if="order.reservation_item?.reservation?.airline">
                                <dt class="text-sm font-medium text-gray-500">Flight Info</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ order.reservation_item?.reservation?.airline }} - {{ order.reservation_item?.reservation?.flight_number }}
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Client Info -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Client Information</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6 grid grid-cols-1 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ order.reservation_item?.reservation?.contact_name }} {{ order.reservation_item?.reservation?.contact_surname }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ order.reservation_item?.reservation?.contact_email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ order.reservation_item?.reservation?.contact_phone }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nationality</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ order.reservation_item?.reservation?.contact_nationality }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Driver & Status -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 text-center">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Current Status</h4>
                        <Badge :color="statusColors[order.status]" class="text-lg px-4 py-2">{{ order.status }}</Badge>
                        <p class="text-xs text-gray-400 mt-2">Last updated: {{ new Date(order.updated_at).toLocaleString() }}</p>
                    </div>

                    <!-- Driver Card -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Assigned Driver</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div v-if="order.driver" class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold mr-3">
                                    {{ order.driver.name.charAt(0) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ order.driver.name }}</div>
                                    <div class="text-xs text-gray-500">{{ order.driver.email }}</div>
                                </div>
                            </div>
                            <div v-else class="text-center text-gray-500 italic py-4">
                                No driver assigned
                            </div>
                            <!-- Vehicle Info -->
                             <div v-if="order.vehicle" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Vehicle</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ order.vehicle.make }} {{ order.vehicle.model }} ({{ order.vehicle.plate }})</dd>
                             </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Activity Log</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <ul class="space-y-4">
                                <li v-for="point in checkpoints" :key="point.key" class="flex items-start">
                                    <div class="flex-shrink-0 h-2 w-2 mt-1.5 rounded-full bg-indigo-500"></div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ point.key }}</p>
                                        <p class="text-xs text-gray-500">{{ point.timestamp }}</p>
                                    </div>
                                </li>
                                <li v-if="checkpoints.length === 0" class="text-sm text-gray-500 italic">No activity recorded yet.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign Modal -->
        <Modal :show="showAssignModal" @close="showAssignModal = false">
             <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ order.driver_id ? 'Reassign' : 'Assign' }} Driver
                </h2>
                <div class="mb-4">
                    <InputLabel for="modal-driver" value="Driver" />
                    <SelectInput id="modal-driver" v-model="assignForm.driver_id" class="mt-1 block w-full">
                        <option value="" disabled>Select a driver...</option>
                        <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                            {{ driver.name }}
                        </option>
                    </SelectInput>
                </div>
                <div class="flex justify-end space-x-2">
                    <SecondaryButton @click="showAssignModal = false">Cancel</SecondaryButton>
                    <PrimaryButton @click="updateAssignment" :disabled="!assignForm.driver_id">Save</PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Status Modal -->
        <Modal :show="showEditStatusModal" @close="showEditStatusModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Update Status</h2>
                <div class="space-y-4">
                    <div>
                        <InputLabel for="status" value="Status" />
                        <SelectInput id="status" v-model="statusForm.status" class="mt-1 block w-full">
                            <option v-for="(color, status) in statusColors" :key="status" :value="status">
                                {{ status }}
                            </option>
                        </SelectInput>
                    </div>
                    <div>
                         <InputLabel for="comments" value="Comments (Optional)" />
                         <TextInput id="comments" v-model="statusForm.comments" class="mt-1 block w-full" />
                    </div>
                </div>
                <div class="flex justify-end space-x-2 mt-6">
                    <SecondaryButton @click="showEditStatusModal = false">Cancel</SecondaryButton>
                    <PrimaryButton @click="updateStatus">Update Status</PrimaryButton>
                </div>
            </div>
        </Modal>

    </AdminLayout>
</template>
