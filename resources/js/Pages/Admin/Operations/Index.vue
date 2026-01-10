<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Badge from '@/Components/Badge.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { format } from 'date-fns';

const props = defineProps({
    orders: Object,
    tab: String,
    drivers: Array,
    filters: Object,
});

const activeTab = ref(props.tab || 'transport');
const assigningOrder = ref(null);
const assignForm = ref({
    driver_id: '',
    vehicle_id: '', // Optional for now
});

const switchTab = (tab) => {
    activeTab.value = tab;
    router.get(route('admin.operations.index'), { tab: tab }, { preserveState: true, preserveScroll: true });
};

const openAssignModal = (order) => {
    assigningOrder.value = order;
    assignForm.value.driver_id = order.driver_id || '';
    // Vehicle logic would go here
};

const closeAssignModal = () => {
    assigningOrder.value = null;
    assignForm.value.driver_id = '';
};

const submitAssignment = () => {
    if (!assignForm.value.driver_id) return;
    
    // We reuse DispatchController's logic or OperationsController update?
    // DispatchController has dedicated 'assign' method which sends notifications.
    // OperationsController is unified. Let's use DispatchController's specific assign route if available, 
    // or call OperationsController update with status change?
    // User asked for "Assign" button. Dispatch assign logic is robust (notifications).
    // Let's use the existing dispatch assign route for consistency.
    router.post(route('admin.dispatch.assign', assigningOrder.value.id), {
        driver_id: assignForm.value.driver_id
    }, {
        onSuccess: () => closeAssignModal()
    });
};

const deleteOrder = (order) => {
    if (confirm('Are you sure you want to remove this order from the list?')) {
        router.delete(route('admin.operations.destroy', order.id));
    }
};

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
    <AdminLayout label="Operations">
        <Head title="Service Operations" />

        <div class="py-6 px-4 max-w-7xl mx-auto">
            <!-- Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button 
                        @click="switchTab('transport')"
                        :class="[activeTab === 'transport' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
                    >
                        Transport
                    </button>
                    <button 
                        @click="switchTab('tours')"
                        :class="[activeTab === 'tours' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
                    >
                        Tours
                    </button>
                    <button 
                        @click="switchTab('packages')"
                        :class="[activeTab === 'packages' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
                    >
                        Packages
                    </button>
                     <button 
                        disabled
                        class="border-transparent text-gray-300 cursor-not-allowed whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    >
                        Baggage (Coming Soon)
                    </button>
                </nav>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" @click="router.visit(route('admin.operations.show', order.id))">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ order.folio }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ order.reservation_item?.reservation?.booking_ref }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ order.reservation_item?.date ? format(new Date(order.reservation_item.date), 'MMM dd, yyyy') : 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ order.reservation_item?.pickup_time }}
                                </div>
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ order.reservation_item?.service_name }}
                                </div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">
                                    {{ order.reservation_item?.pickup_location }} → {{ order.reservation_item?.dropoff_location }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ order.reservation_item?.reservation?.contact_name }} {{ order.reservation_item?.reservation?.contact_surname }}
                                </div>
                                 <div class="text-xs text-gray-500">
                                    {{ order.reservation_item?.reservation?.contact_email }}
                                </div>
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="order.driver" class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold mr-2">
                                        {{ order.driver.name.charAt(0) }}
                                    </div>
                                    <div class="text-sm text-gray-900 dark:text-white">{{ order.driver.name }}</div>
                                </div>
                                <span v-else class="text-sm text-gray-400 italic">Unassigned</span>
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap">
                                <Badge :color="statusColors[order.status]">{{ order.status }}</Badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" @click.stop>
                                <!-- Actions -->
                                <div class="flex justify-end space-x-2">
                                    <button @click="openAssignModal(order)" class="text-indigo-600 hover:text-indigo-900">
                                        {{ order.driver_id ? 'Reassign' : 'Assign' }}
                                    </button>
                                    <Link :href="route('admin.operations.show', order.id)" class="text-gray-600 hover:text-gray-900">
                                        View
                                    </Link>
                                    <button @click="deleteOrder(order)" class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="orders.data.length === 0">
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No service orders found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
             <div class="mt-4">
                <Pagination :links="orders.links" />
            </div>
        </div>

        <!-- Assignment Modal -->
        <Modal :show="!!assigningOrder" @close="closeAssignModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Assign Driver to {{ assigningOrder?.folio }}
                </h2>
                <div class="mb-4">
                    <InputLabel for="driver" value="Select Driver" />
                    <SelectInput id="driver" v-model="assignForm.driver_id" class="mt-1 block w-full">
                        <option value="" disabled>Choose a driver...</option>
                        <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                            {{ driver.name }}
                        </option>
                    </SelectInput>
                </div>
                <div class="flex justify-end space-x-2">
                    <SecondaryButton @click="closeAssignModal">Cancel</SecondaryButton>
                    <PrimaryButton @click="submitAssignment" :disabled="!assignForm.driver_id">
                        {{ assigningOrder?.driver_id ? 'Reassign' : 'Assign' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
