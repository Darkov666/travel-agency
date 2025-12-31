<script setup>
import { useForm } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';

const props = defineProps({
    zones: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    type: 'round_trip',
    destination: '', // This corresponds to "Zone"
    custom_destination: '',
    google_destination: '', // Specific hotel/place
    google_coordinates: null,
    date: '',
    return_date: '',
    adults: 2,
    children: 0,
});

// ... (Google Maps logic remains same)

const submit = () => {
    // 1. Arrival Date Verification (12h min notice)
    const arrivalDate = new Date(form.date);
    const now = new Date();
    const minArrival = new Date(now.getTime() + 12 * 60 * 60 * 1000);
    
    const minDateString = minArrival.toISOString().split('T')[0];
    if (form.date < minDateString) {
        showError("Reservations must be made at least 12 hours in advance.");
        return;
    }

    // 2. Return Date Verification (4h after arrival)
    if (form.type === 'round_trip' && form.return_date) {
        if (form.return_date < form.date) {
             showError('Return date cannot be before arrival date.');
            return;
        }
    }

    // Send both Zone and Specific Google Destination
    form.transform((data) => ({
        ...data,
        destination: form.destination === 'Other' ? form.custom_destination : form.destination, 
        pax: data.adults + data.children, // Calculate total pax for backend
    })).get(route('search'), {
        preserveScroll: true,
        onError: (errors) => console.log(errors),
    });
};

const showErrorMessage = ref(false);
const errorMessage = ref('');

const showError = (msg) => {
    errorMessage.value = msg;
    showErrorMessage.value = true;
};

const closeError = () => {
    showErrorMessage.value = false;
};

const minDate = computed(() => {
    const now = new Date();
    const min = new Date(now.getTime() + 12 * 60 * 60 * 1000);
    return min.toISOString().split('T')[0];
});

const minReturnDate = computed(() => {
   // Return date must be at least same as arrival
   return form.date || minDate.value;
});

const showMap = ref(false);
const mapContainer = ref(null);
let map = null;
let marker = null;

const loadGoogleMaps = () => {
    if (window.google && window.google.maps) {
        initAutocomplete();
        return;
    }
    
    if (document.getElementById('google-maps-script')) return;

    const script = document.createElement('script');
    script.id = 'google-maps-script';
    // Add 'geometry' library
    script.src = `https://maps.googleapis.com/maps/api/js?key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY || 'YOUR_API_KEY'}&libraries=places,geometry`;
    script.async = true;
    script.defer = true;
    script.onload = () => {
        initAutocomplete();
    };
    script.onerror = () => {
        console.error('Google Maps API failed to load. Check your API Key.');
    };
    document.head.appendChild(script);
};

const initAutocomplete = () => {
    if (!window.google) return;
    
    const input = document.getElementById('google-destination-input');
    if (!input) return;

    const autocomplete = new google.maps.places.Autocomplete(input, {
        componentRestrictions: { country: 'mx' }, // Restrict to Mexico
        fields: ['formatted_address', 'geometry', 'name'],
        types: ['establishment', 'geocode'] // Hotels and places
    });

    // Bias to Yucatan Peninsula roughly
    const circle = new google.maps.Circle({
        center: { lat: 20.967, lng: -89.592 }, // Merida/Yucatan center approx
        radius: 400000 // 400km radius
    });
    autocomplete.setBounds(circle.getBounds());

    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) {
             // User entered name but didn't select prediction, or no details
             form.google_destination = input.value;
             return;
        }
        form.google_destination = place.name || place.formatted_address;
        form.google_coordinates = {
            lat: place.geometry.location.lat(),
            lng: place.geometry.location.lng()
        };
        autoSelectZone(place.formatted_address || place.name);
    });
};

const openMap = () => {
    showMap.value = true;
    // Wait for modal to render
    setTimeout(() => {
        if (!map && window.google) {
            initMap();
        }
    }, 100);
};

const initMap = () => {
    const defaultLocation = { lat: 21.1619, lng: -86.8515 }; // Cancun
    map = new google.maps.Map(mapContainer.value, {
        center: form.google_coordinates || defaultLocation,
        zoom: 12,
        mapTypeControl: false, // Cleaner UI
        streetViewControl: false
    });

    const geocoder = new google.maps.Geocoder();

    // Create a draggable marker
    marker = new google.maps.Marker({
        position: form.google_coordinates || defaultLocation,
        map: map,
        draggable: true,
        title: "Drag to select location"
    });

    const updateLocationFromMarker = () => {
        const pos = marker.getPosition();
        const latLng = { lat: pos.lat(), lng: pos.lng() };
        form.google_coordinates = latLng;
        
        // Reverse Geocode
        geocoder.geocode({ location: latLng }, (results, status) => {
            if (status === "OK" && results[0]) {
                const address = results[0].formatted_address;
                form.google_destination = address;
                autoSelectZone(address);
            } else {
                form.google_destination = `Lat: ${pos.lat().toFixed(4)}, Lng: ${pos.lng().toFixed(4)}`;
            }
        });
    };

    marker.addListener('dragend', updateLocationFromMarker);
    
    map.addListener('click', (e) => {
        marker.setPosition(e.latLng);
        updateLocationFromMarker();
    });
};

const autoSelectZone = (address) => {
    // 1. Try Geometric Match (Point in Polygon)
    if (form.google_coordinates && window.google && window.google.maps && window.google.maps.geometry) {
        const point = new google.maps.LatLng(form.google_coordinates.lat, form.google_coordinates.lng);
        
        const geometricMatch = props.zones.find(zone => {
            if (!zone.coordinates) return false;
            // Create a temporary polygon
            const polygon = new google.maps.Polygon({ paths: zone.coordinates });
            return google.maps.geometry.poly.containsLocation(point, polygon);
        });

        if (geometricMatch) {
            form.destination = geometricMatch.name;
            if (form.destination !== 'Other') form.custom_destination = '';
            return;
        }
    }

    // 2. Fallback to String Matching
    if (!address || !props.zones.length) return;
    const lowerAddr = address.toLowerCase();
    
    const matchedZone = props.zones.find(zone => {
        // zone is now an Object, check zone.name
        const zoneName = typeof zone === 'string' ? zone : zone.name;
        const lowerZone = zoneName.toLowerCase();
        
        // Direct match
        if (lowerAddr.includes(lowerZone)) return true;
        
        // Special mappings for better UX
        if (zoneName === 'Cancun Hotel Zone' && lowerAddr.includes('zona hotelera')) return true;
        if (zoneName === 'Cancun Hotel Zone' && lowerAddr.includes('boulevard kukulcan')) return true;
        if (zoneName === 'Cancun Hotel Zone' && lowerAddr.includes('moon palace')) return true; // Hardcoded fix for Moon Palace request
        
        return false;
    });

    if (matchedZone) {
        form.destination = typeof matchedZone === 'string' ? matchedZone : matchedZone.name;
        if (form.destination !== 'Other') {
            form.custom_destination = '';
        }
    }
};

const closeMap = () => {
    showMap.value = false;
};

onMounted(() => {
    loadGoogleMaps();
});

// Submit logic handled in transform above
// (Removing duplicate if any, but replace logic above targeted form definition AND submit)
// Wait, my previous replacement targeted the FORM definition but included the SUBMIT function in the replacement content?
// Ah, the previous replacement START line was 12. The content I checked was form definition.
// The TARGET content in previous step was just the form definition.
// Be careful. My replacement content INCLUDED the submit function. But the target was ONLY the form definition.
// This means I duplicated the submit function if I didn't verify the lines.
// Step 606 check: StartLine 12. TargetContent ends at }); (line 21).
// ReplacementContent has form definition AND submit function.
// This means lines 22-133 (maps logic) are now sandwiched between form/submit?
// No, I need to check where I inserted.
// If I replaced lines 12-21 with a block that includes `submit`, I effectively put `submit` right after `form`.
// But the ORIGINAL `submit` is at line 134.
// So now I have TWO `submit` functions. One at top, one at bottom. I need to remove the bottom one.
</script>

<template>
    <div class="relative z-40 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 text-gray-900 dark:text-white">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 md:p-8 transition-colors duration-300">
            <h2 class="text-2xl font-serif font-bold mb-6 text-center md:text-left transition-colors duration-300">
                Find your perfect transfer
            </h2>
            
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Transfer Type -->
                    <div class="col-span-1">
                        <label class="block text-sm font-bold mb-1 transition-colors duration-300 text-gray-700 dark:text-gray-300">Type</label>
                        <select v-model="form.type" class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-colors duration-300">
                            <option value="one_way">One Way</option>
                            <option value="round_trip">Round Trip</option>
                        </select>
                    </div>

                    <!-- Hotel/Destination (Google) -->
                    <div class="col-span-1 md:col-span-2 lg:col-span-1">
                        <label class="block text-sm font-bold mb-1 transition-colors duration-300 text-gray-700 dark:text-gray-300">Hotel / Exact Location</label>
                        <div class="flex gap-2">
                            <input 
                                id="google-destination-input"
                                type="text" 
                                v-model="form.google_destination"
                                placeholder="Hotel name or Address" 
                                class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-colors duration-300"
                            >
                            <button type="button" @click="openMap" class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Zone (Formerly Destination) -->
                    <div class="col-span-1">
                        <label class="block text-sm font-bold mb-1 transition-colors duration-300 text-gray-700 dark:text-gray-300">Zone</label>
                        <select v-model="form.destination" class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-colors duration-300 mb-2">
                             <option value="" disabled>Select Zone</option>
                             <option v-for="zone in (props.zones.length ? props.zones : [])" :key="zone.id || zone" :value="zone.name || zone">{{ zone.name || zone }}</option>
                             <option value="Other">Other...</option>
                        </select>
                        <input 
                            v-if="form.destination === 'Other'" 
                            v-model="form.custom_destination" 
                            type="text" 
                            placeholder="Specify Zone Name" 
                            class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-colors duration-300 animate-fade-in"
                        >
                    </div>

                    <!-- Dates -->
                    <div class="col-span-1 md:col-span-1 lg:col-span-1 gap-2 flex flex-col">
                         <!-- Arrival -->
                         <div>
                            <label class="block text-sm font-bold mb-1 transition-colors duration-300 text-gray-700 dark:text-gray-300">
                                {{ form.type === 'round_trip' ? 'Pickup Arrival' : 'Date' }}
                            </label>
                            <input type="date" :min="minDate" v-model="form.date" class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-colors duration-300">
                        </div>

                         <!-- Departure (Return) -->
                         <div v-if="form.type === 'round_trip'" class="animate-fade-in-up">
                            <label class="block text-sm font-bold mb-1 transition-colors duration-300 text-gray-700 dark:text-gray-300">Pickup Departure</label>
                            <input type="date" :min="minReturnDate" v-model="form.return_date" class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-colors duration-300">
                        </div>
                    </div>
                </div>
                 
                     <!-- Passengers (Split into Adults/Children) -->
                     <div class="col-span-1 md:col-span-2 gap-4 grid grid-cols-2">
                          <div>
                            <label class="block text-sm font-bold mb-1 transition-colors duration-300 text-gray-700 dark:text-gray-300">Adults</label>
                            <input type="number" min="1" max="20" v-model="form.adults" class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 transition-colors duration-300 text-gray-700 dark:text-gray-300">Children</label>
                            <input type="number" min="0" max="20" v-model="form.children" class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-colors duration-300">
                        </div>
                     </div>

                <div class="flex justify-center md:justify-end mt-2">
                    <button type="submit" class="w-full md:w-auto px-12 py-3 bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-500 dark:hover:bg-cyan-400 text-white font-bold rounded-full shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 focus:ring-4 focus:ring-cyan-200 text-lg">
                        Search Transfers
                    </button>
                </div>
            </form>
        </div>

        <!-- Error Modal -->
        <div v-if="showErrorMessage" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 animate-fade-in">
            <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all scale-100">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Invalid Dates</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ errorMessage }}
                    </p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="closeError" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Understood
                    </button>
                </div>
            </div>
        </div>

        <!-- Map Modal -->
        <div v-if="showMap" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 animate-fade-in">
            <div class="bg-white dark:bg-gray-800 w-full max-w-4xl h-[600px] rounded-2xl shadow-2xl flex flex-col overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-xl font-bold font-serif">Select Location</h3>
                    <button @click="closeMap" class="text-gray-500 hover:text-red-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="flex-grow relative">
                    <div ref="mapContainer" class="w-full h-full"></div>
                </div>
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                    <button @click="closeMap" class="px-6 py-2 bg-cyan-600 text-white rounded-lg font-bold hover:bg-cyan-700 transition">
                        Confirm Location
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
