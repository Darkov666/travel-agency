<script setup>
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import Swal from 'sweetalert2';
import axios from 'axios';

const props = defineProps({
    provider: Object, // Null if creating
    availableServices: Array,
    availableZones: Array,
    allowedTypes: {
        type: Array,
        default: () => ['transport', 'tour', 'water']
    },
    allOrganizations: { // Only passed if Root
        type: Array,
        default: () => []
    }
});

const isEditing = computed(() => !!props.provider);
const page = usePage();

// Determine if we should show Services tab
// Logic: If tenant has ONLY 'transport', hide it (unless user overrides).
const hasNonTransportModules = computed(() => {
    if (!page.props.tenant) return true; // Root
    const modules = page.props.tenant.modules || [];
    return modules.includes('tours') || modules.includes('shop');
});

const activeTab = ref('general');
const tabs = computed(() => [
    { id: 'general', name: 'General Information' },
    { id: 'vehicles', name: 'Fleet / Units', show: isEditing.value && props.provider?.provider_type === 'transport' },
    { 
        id: 'services', 
        name: hasNonTransportModules.value ? 'Products & Services' : 'Service Rates', 
        show: isEditing.value 
    },
]);

// Helper to format allowed types for display
const formatType = (type) => {
    const labels = {
        transport: 'Transportation',
        tour: 'Tours & Attractions',
        water: 'Water Transport',
        baggage: 'Baggage Handling',
        groups_lodging: 'Groups & Lodging'
    };
    return labels[type] || type.charAt(0).toUpperCase() + type.slice(1);
};

const generalForm = useForm({
    name: props.provider?.name || '',
    partner_id: props.provider?.partner_id || '',
    contact_name: props.provider?.contact_name || '',
    email: props.provider?.email || '',
    phone: props.provider?.phone || '',
    provider_type: props.provider?.provider_type || (props.allowedTypes.length === 1 ? props.allowedTypes[0] : 'transport'),
    taxpayer_type: props.provider?.taxpayer_type || 'legal',
    full_address: props.provider?.full_address || '',
    priority: props.provider?.priority || 1,
    is_active: props.provider?.is_active ?? true,
    exchange_rate: props.provider?.organization?.exchange_rate || 20.0,
    logo: null,
    tax_compliance: null,
    assigned_organizations: props.provider?.assigned_organizations?.map(o => o.id) || [],
});

const submitGeneral = () => {
    if (isEditing.value) {
        // Use POST with _method: PUT for file uploads
        generalForm.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('admin.providers.update', props.provider.id), {
            onSuccess: () => Swal.fire('Saved', 'Provider updated', 'success'),
        });
    } else {
        generalForm.post(route('admin.providers.store'));
    }
};

// --- Vehicles Logic ---
const editingVehicleId = ref(null);
const vehicleForm = ref({
    provider_id: props.provider?.id,
    model_name: '',
    type: 'van',
    max_pax: 10,
    category: 'standard',
    image: null
});

const editVehicle = (vehicle) => {
    editingVehicleId.value = vehicle.id;
    vehicleForm.value = {
        provider_id: vehicle.provider_id,
        model_name: vehicle.model_name,
        type: vehicle.type,
        max_pax: vehicle.max_pax,
        category: vehicle.category,
        image: null // Don't prefill file input
    };
};

const cancelEditVehicle = () => {
    editingVehicleId.value = null;
    vehicleForm.value = {
        provider_id: props.provider?.id,
        model_name: '',
        type: 'van',
        max_pax: 10,
        category: 'standard',
        image: null
    };
};

const addVehicle = async () => {
    try {
        const formData = new FormData();
        formData.append('provider_id', vehicleForm.value.provider_id);
        formData.append('model_name', vehicleForm.value.model_name);
        formData.append('type', vehicleForm.value.type);
        formData.append('max_pax', vehicleForm.value.max_pax);
        formData.append('category', vehicleForm.value.category);
        if (vehicleForm.value.image) {
            formData.append('image', vehicleForm.value.image);
        }

        if (editingVehicleId.value) {
            formData.append('_method', 'PUT'); // Spoof for file upload
            await axios.post(route('admin.vehicles.update', editingVehicleId.value), formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            Swal.fire('Updated', 'Vehicle info updated', 'success');
        } else {
            await axios.post(route('admin.vehicles.store'), formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            Swal.fire('Added', 'Vehicle added to fleet', 'success');
        }
        
        router.reload({ only: ['provider'] }); 
        cancelEditVehicle();
        
    } catch (e) {
        console.error(e);
        Swal.fire('Error', 'Failed to save vehicle', 'error');
    }
};

const deleteVehicle = async (id) => {
    if (!confirm('Remove vehicle?')) return;
    try {
        await axios.delete(route('admin.vehicles.destroy', id));
        router.reload({ only: ['provider'] });
    } catch (e) { Swal.fire('Error', 'Failed to remove', 'error'); }
};

// --- Services Logic ---
const editingServiceId = ref(null);
const serviceForm = ref({
    provider_id: props.provider?.id,
    zone_id: '',
    service_id: '',
    name: '', // Custom name
    description: '',
    cost_net: 0,
    price_public: 0,
    max_pax: 10,
    category: 'standard'
});

const editService = (item) => {
    editingServiceId.value = item.id;
    serviceForm.value = {
        provider_id: item.provider_id,
        zone_id: item.zone_id || '',
        service_id: item.service_id || '',
        name: item.name || '',
        description: item.description || '',
        cost_net: item.cost_net,
        price_public: item.price_public,
        max_pax: item.max_pax || 10,
        category: item.category || 'standard'
    };
};

const cancelEditService = () => {
    editingServiceId.value = null;
    serviceForm.value = {
        provider_id: props.provider?.id,
        zone_id: '',
        service_id: '',
        name: '',
        description: '',
        cost_net: 0,
        price_public: 0,
        max_pax: 10,
        category: 'standard'
    };
};

const addService = async () => {
    try {
        if (editingServiceId.value) {
            await axios.put(route('admin.provider-services.update', editingServiceId.value), serviceForm.value);
            Swal.fire('Updated', 'Service updated', 'success');
        } else {
            await axios.post(route('admin.provider-services.store'), serviceForm.value);
            Swal.fire('Added', 'Service assigned', 'success');
        }
        router.reload({ only: ['provider'] });
        cancelEditService();
    } catch (e) {
        Swal.fire('Error', 'Failed to save service', 'error');
    }
};

const deleteService = async (id) => {
    if (!confirm('Remove service?')) return;
     try {
        await axios.delete(route('admin.provider-services.destroy', id));
        router.reload({ only: ['provider'] });
    } catch (e) { Swal.fire('Error', 'Failed to remove', 'error'); }
};

</script>

<template>
    <AdminLayout>
        <Head title="Manage Provider" />
        
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ isEditing ? 'Edit Provider: ' + provider.name : 'Create New Provider' }}
            </h2>
        </div>

        <!-- TABS -->
        <div v-if="isEditing" class="border-b border-gray-200 dark:border-gray-700 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button 
                    v-for="tab in tabs.filter(t => t.show !== false)" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        activeTab === tab.id
                            ? 'border-cyan-500 text-cyan-600'
                            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300',
                        'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium'
                    ]"
                >
                    {{ tab.name }}
                </button>
            </nav>
        </div>

        <!-- GENERAL TAB -->
        <div v-if="activeTab === 'general'" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form @submit.prevent="submitGeneral" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name</label>
                        <input v-model="generalForm.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Partner ID (Identifier)</label>
                        <input 
                            v-model="generalForm.partner_id" 
                            type="text" 
                            :readonly="!isEditing"
                            :placeholder="isEditing ? '' : 'Auto-generated'"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white dark:border-gray-600"
                            :class="{'bg-gray-100 text-gray-500': !isEditing}"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exchange Rate (USD to MXN)</label>
                        <input v-model="generalForm.exchange_rate" type="number" step="0.0001" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Provider Type</label>
                        <select v-model="generalForm.provider_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            <option v-for="type in allowedTypes" :key="type" :value="type">
                                {{ formatType(type) }}
                            </option>
                        </select>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Taxpayer Type</label>
                        <select v-model="generalForm.taxpayer_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            <option value="legal">Legal Entity (Moral)</option>
                            <option value="physical">Individual (Física)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                         <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Person</label>
                        <input v-model="generalForm.contact_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    </div>
                     <div>
                         <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input v-model="generalForm.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    </div>
                     <div>
                         <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <input v-model="generalForm.phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority Listing (1 = Top)</label>
                        <select v-model="generalForm.priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            <option :value="1">Option 1 (Highest)</option>
                            <option :value="2">Option 2</option>
                            <option :value="3">Option 3 (Lowest)</option>
                        </select>
                    </div>
                     <div class="flex items-center">
                        <input v-model="generalForm.is_active" type="checkbox" id="is_active" class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-white">Active Status</label>
                    </div>
                </div>

                <div>
                     <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Address</label>
                    <textarea v-model="generalForm.full_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white dark:border-gray-600"></textarea>
                </div>

                <!-- Shared Organizations (Root Only) -->
                <div v-if="allOrganizations.length > 0" class="border-t pt-4 mt-4 border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Share with Organizations (SaaS)</h4>
                    <p class="text-xs text-gray-500 mb-3">Selected organizations will be able to see and use this provider.</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-40 overflow-y-auto p-2 bg-gray-50 dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700">
                        <div v-for="org in allOrganizations" :key="org.id" class="flex items-center">
                            <input 
                                type="checkbox" 
                                :value="org.id" 
                                v-model="generalForm.assigned_organizations"
                                :id="`org-${org.id}`"
                                class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded"
                            >
                            <label :for="`org-${org.id}`" class="ml-2 block text-xs text-gray-700 dark:text-gray-300 truncate">
                                {{ org.name }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logo (Image)</label>
                        <input type="file" @input="generalForm.logo = $event.target.files[0]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 dark:text-gray-300">
                         <div v-if="provider?.logo_path" class="mt-2">
                            <img :src="`/storage/${provider.logo_path}`" class="h-16 w-auto rounded">
                        </div>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax Compliance (PDF)</label>
                        <input type="file" @input="generalForm.tax_compliance = $event.target.files[0]" accept=".pdf,image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 dark:text-gray-300">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" :disabled="generalForm.processing" class="bg-cyan-600 text-white px-4 py-2 rounded-md hover:bg-cyan-700">
                        {{ isEditing ? 'Update General Info' : 'Create Provider' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- VEHICLES TAB -->
        <div v-if="activeTab === 'vehicles'" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Fleet Management</h3>
            
            <!-- Quick Add Form -->
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6 flex flex-wrap gap-4 items-end">
                <div>
                     <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Model</label>
                    <input v-model="vehicleForm.model_name" type="text" placeholder="e.g. Toyota Hiace" class="rounded border-gray-300 dark:bg-gray-600 dark:border-gray-500 text-sm">
                </div>
                 <div>
                     <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Type</label>
                    <select v-model="vehicleForm.type" class="rounded border-gray-300 dark:bg-gray-600 dark:border-gray-500 text-sm">
                         <option value="van">Van</option>
                         <option value="suv">SUV</option>
                         <option value="bus">Bus</option>
                         <option value="boat">Boat</option>
                         <option value="catamaran">Catamaran</option>
                    </select>
                </div>
                <div>
                     <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Max Pax</label>
                    <input v-model="vehicleForm.max_pax" type="number" class="w-20 rounded border-gray-300 dark:bg-gray-600 dark:border-gray-500 text-sm">
                </div>
                 <div>
                     <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select v-model="vehicleForm.category" class="rounded border-gray-300 dark:bg-gray-600 dark:border-gray-500 text-sm">
                         <option value="standard">Standard</option>
                         <option value="vip">VIP</option>
                    </select>
                </div>
                 <div>
                     <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Image</label>
                    <input type="file" @input="vehicleForm.image = $event.target.files[0]" accept="image/*" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 dark:text-gray-300">
                </div>
                <button @click="addVehicle" class="bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                    {{ editingVehicleId ? 'Update Unit' : 'Add Unit' }}
                </button>
                <button v-if="editingVehicleId" @click="cancelEditVehicle" class="bg-gray-500 text-white px-3 py-2 rounded text-sm hover:bg-gray-600">
                    Cancel
                </button>
            </div>

            <!-- List -->
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pax</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-3 py-2 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="vehicle in provider.vehicles" :key="vehicle.id">
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <img v-if="vehicle.image_path" :src="`/storage/${vehicle.image_path}`" class="h-10 w-16 object-cover rounded" alt="Vehicle">
                            <span v-else class="text-xs text-gray-400">No Img</span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ vehicle.model_name }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ vehicle.type }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ vehicle.max_pax }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            <span :class="vehicle.category === 'vip' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                {{ vehicle.category }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="editVehicle(vehicle)" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                            <button @click="deleteVehicle(vehicle.id)" class="text-red-600 hover:text-red-900">Remove</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- SERVICES TAB -->
        <div v-if="activeTab === 'services'" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product & Pricing Assignment</h3>

             <!-- Quick Add Form -->
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg mb-6 flex flex-wrap gap-4 items-end">
                <div class="w-full md:w-auto">
                     <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Zone (Destination)</label>
                     <select v-model="serviceForm.zone_id" class="w-full rounded border-gray-300 dark:bg-gray-600 dark:border-gray-500 text-sm">
                         <option value="">-- Generic / Any --</option>
                         <option v-for="zone in availableZones" :key="zone.id" :value="zone.id">{{ zone.name }}</option>
                    </select>
                </div>
                <div class="w-full md:w-auto">
                     <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Service (Catalog)</label>
                     <select v-model="serviceForm.service_id" class="w-full rounded border-gray-300 dark:bg-gray-600 dark:border-gray-500 text-sm">
                         <option value="">Select or Custom...</option>
                         <option v-for="svc in availableServices" :key="svc.id" :value="svc.id">{{ svc.title }} ({{ svc.type }})</option>
                    </select>
                </div>
                 <div v-if="!serviceForm.service_id" class="w-full md:w-auto">
                     <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Custom Name</label>
                    <input v-model="serviceForm.name" type="text" placeholder="Custom Service" class="w-full rounded border-gray-300 dark:bg-gray-600 dark:border-gray-500 text-sm">
                </div>
                 <div>
                     <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Net Cost</label>
                    <input v-model="serviceForm.cost_net" type="number" class="w-24 rounded border-gray-300 dark:bg-gray-600 dark:border-gray-500 text-sm">
                </div>
                 <div>
                     <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Public Price</label>
                    <input v-model="serviceForm.price_public" type="number" class="w-24 rounded border-gray-300 dark:bg-gray-600 dark:border-gray-500 text-sm">
                </div>
                  <div>
                     <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select v-model="serviceForm.category" class="rounded border-gray-300 dark:bg-gray-600 dark:border-gray-500 text-sm">
                         <option value="standard">Standard</option>
                         <option value="vip">VIP</option>
                    </select>
                </div>

                <button @click="addService" class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                     {{ editingServiceId ? 'Update Service' : 'Assign' }}
                </button>
                 <button v-if="editingServiceId" @click="cancelEditService" class="bg-gray-500 text-white px-3 py-2 rounded text-sm hover:bg-gray-600">
                    Cancel
                </button>
            </div>

            <!-- List -->
             <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Zone</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Net Cost</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Public Price</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-3 py-2 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="item in provider.provider_services" :key="item.id">
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ item.zone?.name || 'Generic' }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ item.service?.title || item.name }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">${{ item.cost_net }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">${{ item.price_public }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ item.category }}</td>
                          <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="editService(item)" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</button>
                            <button @click="deleteService(item.id)" class="text-red-600 hover:text-red-900">Remove</button>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

    </AdminLayout>
</template>
