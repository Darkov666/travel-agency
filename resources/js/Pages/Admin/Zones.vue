<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { onMounted, ref, watch, toRaw, markRaw, computed } from 'vue';

const props = defineProps({
    zones: Array,
    providers: Array
});

const filterProvider = ref('all');
const filterType = ref('all');

const filteredZones = computed(() => {
    return props.zones.filter(zone => {
        const matchesProvider = filterProvider.value === 'all' || 
            (filterProvider.value === 'admin' ? !zone.provider_id : zone.provider_id == filterProvider.value);
        
        const matchesType = filterType.value === 'all' || zone.service_type === filterType.value;

        return matchesProvider && matchesType;
    });
});

const apiKeyConfigured = ref(!!import.meta.env.VITE_GOOGLE_MAPS_API_KEY);

const mapContainer = ref(null);
let map = null;
let drawingManager = null;
let polygons = []; // Store references to drawn polygons to handle selection/editing

const editingZone = ref(null); // The zone currently being edited (or new)
const showModal = ref(false);

const form = useForm({
    id: null,
    name: '',
    priority: 0,
    transfer_time_minutes: 60,
    color: '#3b82f6',
    coordinates: null,
    provider_id: null,
    service_type: 'transfer'
});

// Load Google Maps with Drawing Library
const loadMaps = () => {
    if (window.google && window.google.maps) {
        initMap();
        return;
    }

    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY}&libraries=drawing`;
    script.async = true;
    script.defer = true;
    script.onload = initMap;
    document.head.appendChild(script);
};

const initMap = () => {
    map = new google.maps.Map(mapContainer.value, {
        center: { lat: 20.65, lng: -87.10 }, // Riviera Maya center
        zoom: 9,
        streetViewControl: false,
    });

    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: null, // Start with no tool selected
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: ['polygon'],
        },
        polygonOptions: {
            fillColor: '#3b82f6',
            fillOpacity: 0.3,
            strokeWeight: 2,
            clickable: true,
            editable: true,
            zIndex: 1
        },
    });

    drawingManager.setMap(map);

    // Event: Polygon Completed
    google.maps.event.addListener(drawingManager, 'polygoncomplete', (polygon) => {
        // Clear drawing mode
        drawingManager.setDrawingMode(null);
        
        // Open modal to save new zone
        editingZone.value = { isNew: true, polygon: markRaw(polygon) };
        form.reset();
        form.coordinates = getPolygonCoords(polygon);
        showModal.value = true;
        
        // Add listeners to this new polygon
        addPolygonListeners(polygon);
    });

    // Load existing zones
    loadZonesOnMap();
};

const loadZonesOnMap = () => {
    // Clear existing
    polygons.forEach(p => p.setMap(null));
    polygons = [];

    polygons = [];

    filteredZones.value.forEach(zone => {
        if (!zone.coordinates) return;
        
        // zone.coordinates is now an Array (Model Cast)
        const paths = zone.coordinates;
        const polygon = new google.maps.Polygon({
            paths: paths,
            fillColor: zone.color,
            fillOpacity: 0.35,
            strokeColor: zone.color,
            strokeWeight: 2,
            editable: false, 
            map: map,
            zIndex: zone.priority
        });

        // Attach data to polygon object for easy access
        polygon.zoneData = zone;
        
        addPolygonListeners(polygon);
        polygons.push(polygon);
    });
};

const addPolygonListeners = (polygon) => {
    google.maps.event.addListener(polygon, 'click', () => {
        if (polygon.zoneData) {
            editZone(polygon.zoneData, polygon);
        }
    });

    // Update coordinates when edited
    const updateCoords = () => {
        // Vue wraps objects in Proxies. We must compare the raw object.
        if (editingZone.value && toRaw(editingZone.value.polygon) === polygon) {
            form.coordinates = getPolygonCoords(polygon);
        }
    };
    polygon.getPath().addListener('set_at', updateCoords);
    polygon.getPath().addListener('insert_at', updateCoords);
    polygon.getPath().addListener('remove_at', updateCoords);
};

const getPolygonCoords = (polygon) => {
    const len = polygon.getPath().getLength();
    const coords = [];
    for (let i = 0; i < len; i++) {
        coords.push({
            lat: polygon.getPath().getAt(i).lat(),
            lng: polygon.getPath().getAt(i).lng()
        });
    }
    return coords; // Return Array, not String
};

const editZone = (zone, polygon) => {
    editingZone.value = { isNew: false, polygon: markRaw(polygon) };
    
    form.id = zone.id;
    form.name = zone.name;
    form.priority = zone.priority;
    form.transfer_time_minutes = zone.transfer_time_minutes || 60;
    form.color = zone.color;
    form.coordinates = zone.coordinates; // Keep existing unless edited
    form.provider_id = zone.provider_id;
    form.service_type = zone.service_type || 'transfer';
    
    // Enable editing on map
    polygons.forEach(p => p.setEditable(false)); // Disable others
    polygon.setEditable(true);

    showModal.value = true;
};

const saveZone = () => {
    if (!form.transfer_time_minutes) form.transfer_time_minutes = 60;

    if (editingZone.value.isNew) {
        form.post(route('admin.zones.store'), {
            onSuccess: () => {
                closeModal(true);
            }
        });
    } else {
        form.put(route('admin.zones.update', form.id), {
            onSuccess: () => {
                closeModal(true);
            }
        });
    }
};

const deleteZone = () => {
    if (!confirm('Are you sure you want to delete this zone?')) return;
    
    form.delete(route('admin.zones.destroy', form.id), {
        onSuccess: () => {
            if (editingZone.value.polygon) {
                editingZone.value.polygon.setMap(null);
            }
            closeModal(true);
        }
    });
};

const closeModal = (reload = false) => {
    showModal.value = false;
    if (editingZone.value && editingZone.value.polygon) {
        editingZone.value.polygon.setEditable(false);
        // If new and cancelled (not reloaded), remove it
        if (editingZone.value.isNew && !reload) {
            editingZone.value.polygon.setMap(null);
        }
    }
    editingZone.value = null;
    form.reset();
    
    if (reload) {
        // Props updated automatically by Inertia, reload map items
        // Wait for next tick? Use Watcher?
    }
};

// Watch for zones prop change to refresh map
watch(() => [props.zones, filterProvider.value, filterType.value], () => {
    loadZonesOnMap();
}, { deep: true });

onMounted(() => {
    loadMaps();
});
</script>

<template>
    <Head title="Zone Editor" />

    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Zone Editor
                </h2>
                <div class="text-sm text-gray-500">
                    Use the toolbar to draw polygons. Click a zone to edit.
                </div>
            </div>
        </template>

        <div class="h-[calc(100vh-180px)] w-full relative">
            <div v-if="!apiKeyConfigured" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Configuration Error</p>
                <p>Google Maps API Key is missing. The map and editing tools will not work. Please add VITE_GOOGLE_MAPS_API_KEY to your .env file.</p>
            </div>
            <div ref="mapContainer" class="w-full h-full"></div>

            <!-- Filters -->
            <div class="absolute top-4 left-4 bg-white dark:bg-gray-800 p-3 rounded shadow-md z-10 flex flex-col space-y-2 w-64">
                <h4 class="font-bold text-sm text-gray-700 dark:text-gray-300">Filters</h4>
                <select v-model="filterProvider" class="text-sm rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                    <option value="all">All Providers</option>
                    <option value="admin">Global / Admin</option>
                    <option v-for="p in providers" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
                <select v-model="filterType" class="text-sm rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                    <option value="all">All Types</option>
                    <option value="transfer">Transfers (Fleet)</option>
                    <option value="tour">Tours / Products</option>
                </select>
            </div>

            <!-- Edit Modal -->
            <div v-if="showModal" class="absolute top-4 right-4 w-80 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-xl z-10">
                <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-white">
                    {{ editingZone?.isNew ? 'New Zone' : 'Edit Zone' }}
                </h3>
                
                <form @submit.prevent="saveZone">
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-300">Name</label>
                        <input v-model="form.name" type="text" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <div class="mb-4 grid grid-cols-2 gap-2">
                        <div>
                             <label class="block text-xs font-bold mb-1 text-gray-700 dark:text-gray-300">Owner (Provider)</label>
                             <select v-model="form.provider_id" class="w-full text-sm rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                                <option :value="null">Global / Admin</option>
                                <option v-for="p in providers" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>
                         <div>
                             <label class="block text-xs font-bold mb-1 text-gray-700 dark:text-gray-300">Type</label>
                             <select v-model="form.service_type" class="w-full text-sm rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                                <option value="transfer">Transfer</option>
                                <option value="tour">Tour/Product</option>
                                <option value="all">All</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                         <label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-300">
                            Priority <span class="font-normal text-xs text-gray-500">(Higher wins)</span>
                        </label>
                        <input v-model="form.priority" type="number" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white">
                    </div>

                    <div class="mb-4">
                         <label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-300">
                            Transfer Time to Airport (min)
                        </label>
                        <input v-model="form.transfer_time_minutes" type="number" min="0" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-1 text-gray-700 dark:text-gray-300">Color</label>
                        <input v-model="form.color" type="color" class="w-full h-10 rounded border-gray-300">
                    </div>

                    <div class="flex justify-between mt-6">
                        <button type="button" @click="closeModal(false)" class="text-gray-500 hover:text-gray-700">Cancel</button>
                        <div>
                            <button v-if="!editingZone.isNew" type="button" @click="deleteZone" class="text-red-500 mr-4 hover:text-red-700">Delete</button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
