<script setup>
import { ref, watch } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Badge from '@/Components/Badge.vue';
import { format } from 'date-fns';

const props = defineProps({
    orders: Object,
    drivers: Array,
    filters: Object,
});

const searchForm = ref({
    status: props.filters.status || '',
    date: props.filters.date || '',
    driver_id: props.filters.driver_id || '',
});

const assignForm = useForm({
    driver_id: '',
    vehicle_id: '', // Optional for now
});

const assigningOrder = ref(null);
const showAssignModal = ref(false);

const openAssignModal = (order) => {
    assigningOrder.value = order;
    assignForm.driver_id = order.driver_id || '';
    assignForm.vehicle_id = order.vehicle_id || '';
    showAssignModal.value = true;
};

const closeAssignModal = () => {
    showAssignModal.value = false;
    assigningOrder.value = null;
    assignForm.reset();
};

const submitAssignment = () => {
    assignForm.post(route('admin.dispatch.assign', assigningOrder.value.id), {
        onSuccess: () => closeAssignModal(),
    });
};

const unassignDriver = (order) => {
    if (confirm('Are you sure you want to unassign the driver?')) {
        router.post(route('admin.dispatch.unassign', order.id));
    }
};

const doSearch = () => {
    router.get(route('admin.dispatch.index'), searchForm.value, { preserveState: true, replace: true });
};

watch(searchForm, () => {
    // Debounce could be added here
}, { deep: true });

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
    <AdminLayout label="Dispatch & Operations">
        <Head title="Dispatch Console" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Filters -->
                <div class="bg-white p-4 rounded-lg shadow mb-6 flex gap-4 items-end">
                    <div>
                        <InputLabel value="Filter by Date" />
                        <TextInput type="date" v-model="searchForm.date" class="mt-1 block w-full" @change="doSearch" />
                    </div>
                    <div>
                        <InputLabel value="Filter by Status" />
                        <SelectInput v-model="searchForm.status" class="mt-1 block w-full" @change="doSearch">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="assigned">Assigned</option>
                            <option value="accepted">Accepted</option>
                            <option value="en_route">Active (En Route)</option>
                        </SelectInput>
                    </div>
                    <div>
                        <InputLabel value="Filter by Driver" />
                        <SelectInput v-model="searchForm.driver_id" class="mt-1 block w-full" @change="doSearch">
                            <option value="">All Drivers</option>
                            <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                                {{ driver.name }}
                            </option>
                        </SelectInput>
                    </div>
                    <SecondaryButton @click="doSearch">Apply Filters</SecondaryButton>
                </div>

                <!-- Orders Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Folio / Service</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">#{{ order.folio }}</div>
                                        <div class="text-xs text-gray-500">{{ order.reservation_item?.service_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ order.reservation_item?.date ? format(new Date(order.reservation_item.date), 'MMM dd, yyyy') : 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ order.reservation_item?.pickup_time || order.reservation_item?.time || 'TBA' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <span class="font-semibold">From:</span> {{ order.reservation_item?.pickup_location || 'Origin' }} <br>
                                            <span class="font-semibold">To:</span> {{ order.reservation_item?.dropoff_location || 'Destination' }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ order.reservation_item?.pax }} Pax
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div v-if="order.driver" class="flex items-center">
                                            <!-- Avatar could go here -->
                                            <div class="ml-0">
                                                <div class="text-sm font-medium text-gray-900">{{ order.driver.name }}</div>
                                                <div class="text-xs text-gray-500">{{ order.vehicle?.plate || 'No Vehicle' }}</div>
                                            </div>
                                        </div>
                                        <div v-else class="text-sm text-yellow-600 italic">Unassigned</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Badge :color="statusColors[order.status]">{{ order.status }}</Badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <PrimaryButton v-if="!order.driver_id" @click="openAssignModal(order)" class="text-xs">
                                            Assign
                                        </PrimaryButton>
                                        <div v-else class="flex justify-end gap-2">
                                            <SecondaryButton @click="openAssignModal(order)" class="text-xs">
                                                Reassign
                                            </SecondaryButton>
                                            <button @click="unassignDriver(order)" class="text-red-600 hover:text-red-900 text-xs ml-2">
                                                Unassign
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="orders.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No active service orders found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Pagination :links="orders.links" class="p-6" />
                </div>
            </div>
        </div>

        <!-- Assignment Modal -->
        <Modal :show="showAssignModal" @close="closeAssignModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Assign Driver</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Assign a driver to Service Order #{{ assigningOrder?.folio }}
                </p>

                <div class="mt-6">
                    <InputLabel for="driver" value="Select Driver" />
                    <SelectInput id="driver" v-model="assignForm.driver_id" class="mt-1 block w-full">
                        <option value="" disabled>Choose a driver...</option>
                        <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                            {{ driver.name }}
                        </option>
                    </SelectInput>
                    <div v-if="assignForm.errors.driver_id" class="text-red-500 text-xs mt-1">
                        {{ assignForm.errors.driver_id }}
                    </div>
                </div>

                <!-- Vehicle Selection is optional/hidden for now as we don't have vehicles prop fully wired -->
                <!-- 
                <div class="mt-4">
                     <InputLabel for="vehicle" value="Select Vehicle (Optional)" />
                     ...
                </div>
                -->

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeAssignModal">Cancel</SecondaryButton>
                    <PrimaryButton class="ml-3" :class="{ 'opacity-25': assignForm.processing }" :disabled="assignForm.processing" @click="submitAssignment">
                        Assign Driver
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
