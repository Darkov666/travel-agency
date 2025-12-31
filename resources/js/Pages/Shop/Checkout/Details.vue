<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    cart: Object,
    user: Object,
});

const form = useForm({
    contact_name: props.user?.name || '',
    contact_surname: '', // If user has split name logic, might need parsing
    contact_email: props.user?.email || '',
    contact_phone: props.user?.phone || '',
    contact_nationality: '',
    
    // Per item holder logic:
    // keys: cart_item.id, value: { same_as_main: true, holder_name: '' }
    holders: {}, 
    
    // keys: cart_item.id, value: { minor_count: 0, list: [] }
    passengers: {},
    
    // keys: cart_item.id, value: { type: 'international', arrival_..., departure_... }
    flights: {}
});

// Initialize form holders and passengers
props.cart.items.forEach(item => {
    form.holders[item.id] = {
        same_as_main: true,
        holder_name: ''
    };
    
    // Default: All adults
    const initialList = Array(item.pax).fill().map(() => ({ type: 'adult', age: null, is_disabled: false }));
    form.passengers[item.id] = {
        minor_count: 0,
        list: initialList
    };
    
    // Init Flight Data
    form.flights[item.id] = {
        type: 'international',
        arrival_airline: '',
        arrival_number: '',
        arrival_time: '',
        arrival_terminal: 'T3', // Common default
        departure_airline: '',
        departure_number: '',
        departure_time: '',
        departure_terminal: 'T3'
    };
});

const updatePassengerList = (itemId, totalPax) => {
    const data = form.passengers[itemId];
    let minors = parseInt(data.minor_count) || 0;
    if (minors < 0) minors = 0;
    if (minors > totalPax) minors = totalPax;
    data.minor_count = minors;
    
    const adults = totalPax - minors;
    const newList = [];
    
    // Add Adults
    for(let i=0; i<adults; i++) {
        // Try to preserve existing data if possible, or reset
        // Simple approach: Reset list or map index? 
        // Let's just create new structure but maybe check if we can keep existing checkbox state?
        // Too complex for now. Resetting is safer to avoid type mismatch (adult becoming minor with age)
        newList.push({ type: 'adult', age: null, is_disabled: false });
    }
    
    // Add Minors
    for(let i=0; i<minors; i++) {
        newList.push({ type: 'minor', age: '', is_disabled: false });
    }
    
    data.list = newList;
};

const submit = () => {
    // Transform holders to flat structure for backend if needed or handle logic
    // We send payload as is? Backend expects 'holders' array?
    // Let's refine payload before submit if needed.
    // Actually, let's map form.holders to simple key-value for backend simplicity
    
    // Validate: At least one adult per item
    for (const [itemId, data] of Object.entries(form.passengers)) {
        const hasAdult = data.list.some(p => p.type === 'adult');
        if (!hasAdult) {
            alert('Each service must have at least one adult passenger.');
            return;
        }
    }

    // Validate Phone format (simple check before backend)
    const phoneRegex = /^[0-9]{10,14}$/;
    if (form.contact_phone && !phoneRegex.test(form.contact_phone)) {
        // Let backend handle detailed error or show alert? form.errors will handle backend response.
        // But let's fail fast if obvious.
        // Actually, let Inertia handle backend validation errors for better UI integration.
    }
    
    let payload_holders = {};
    for (const [itemId, data] of Object.entries(form.holders)) {
        if (!data.same_as_main) {
            payload_holders[itemId] = data.holder_name;
        }
    }
    
    form.transform((data) => ({
        ...data,
        holders: payload_holders
    })).post(route('checkout.store_details'));
};

</script>

<template>
    <Head title="Checkout - Details" />

    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-serif font-bold text-secondary-900 dark:text-white mb-8">
                    Contact Details
                </h1>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Form -->
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
                            <h2 class="text-xl font-bold text-secondary-900 dark:text-white mb-4">Main Contact (Titular)</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                    <input v-model="form.contact_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <div v-if="form.errors.contact_name" class="text-red-500 text-xs mt-1">{{ form.errors.contact_name }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Surname</label>
                                    <input v-model="form.contact_surname" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <div v-if="form.errors.contact_surname" class="text-red-500 text-xs mt-1">{{ form.errors.contact_surname }}</div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input v-model="form.contact_email" type="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <div v-if="form.errors.contact_email" class="text-red-500 text-xs mt-1">{{ form.errors.contact_email }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                    <input v-model="form.contact_phone" type="tel" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <div v-if="form.errors.contact_phone" class="text-red-500 text-xs mt-1">{{ form.errors.contact_phone }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Item Specifics -->
                        <div v-for="item in cart.items" :key="item.id" class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-lg text-secondary-900 dark:text-white mb-2">
                                {{ item.provider_service.service?.title || item.provider_service.name }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                <span v-if="item.provider_service.zone" class="font-semibold text-primary-600 dark:text-primary-400">{{ item.provider_service.zone.name }}</span> 
                                <span class="mx-1">•</span> {{ item.pax }} Pax <span class="mx-1">•</span> {{ item.date }}
                            </p>

                            <!-- Titular Override (Only for Tours/Others or if needed) -->
                             <!-- Requirement: Transport same, but specific option for other services -->
                            <div class="mb-4" v-if="item.provider_service.service?.type !== 'transfer'">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" v-model="form.holders[item.id].same_as_main" class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Holder is same as Main Contact</span>
                                </label>
                                
                                <div v-if="!form.holders[item.id].same_as_main" class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service Holder Name</label>
                                    <input v-model="form.holders[item.id].holder_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                </div>
                            </div>

                            <!-- Passenger Details -->
                            <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-4">
                                <h4 class="font-bold text-md text-secondary-900 dark:text-white mb-2">Passive Details</h4>
                               
                                <!-- Counts -->
                                <div class="flex space-x-4 mb-4">
                                    <div class="flex-1">
                                        <label class="block text-xs font-bold text-gray-500 uppercase">Total Pax</label>
                                        <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ item.pax }}</div>
                                    </div>
                                    <div class="flex-1">
                                         <label class="block text-xs font-bold text-gray-500 uppercase">Minors</label>
                                         <input type="number" min="0" :max="item.pax" v-model="form.passengers[item.id].minor_count" @input="updatePassengerList(item.id, item.pax)" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 h-8 text-sm">
                                    </div>
                                </div>

                                <!-- Dynamic List -->
                                <div class="space-y-3">
                                    <div v-for="(pax, index) in form.passengers[item.id].list" :key="index" class="flex items-center space-x-4 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                                        <div class="flex-none w-20">
                                            <span class="text-xs font-bold uppercase" :class="pax.type === 'adult' ? 'text-blue-600 dark:text-blue-400' : 'text-pink-600 dark:text-pink-400'">
                                                {{ pax.type }} {{ index + 1 }}
                                            </span>
                                        </div>
                                        
                                        <!-- Age Input for Minors -->
                                        <div class="flex-1">
                                            <div v-if="pax.type === 'minor'">
                                                <input type="number" placeholder="Age" v-model="pax.age" class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                                            </div>
                                            <div v-else class="text-xs text-gray-400">Adult</div>
                                        </div>

                                        <!-- Disability Switch -->
                                        <div class="flex items-center">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="checkbox" v-model="pax.is_disabled" class="sr-only peer">
                                                <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                                <span class="ms-2 text-xs font-medium text-gray-900 dark:text-gray-300">Disability?</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Flight Details -->
                            <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-4" v-if="item.provider_service.service?.type === 'transfer' || item.provider_service.type === 'transfer'">
                                <h4 class="font-bold text-md text-secondary-900 dark:text-white mb-4">Flight Information</h4>
                                
                                <div class="mb-4">
                                     <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Flight Type</label>
                                     <div class="flex space-x-4">
                                         <label class="flex items-center">
                                            <input type="radio" v-model="form.flights[item.id].type" value="international" class="text-primary-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">International (3h prior)</span>
                                         </label>
                                         <label class="flex items-center">
                                            <input type="radio" v-model="form.flights[item.id].type" value="local" class="text-primary-600 focus:ring-primary-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">National (2h prior)</span>
                                         </label>
                                     </div>
                                </div>

                                <!-- Arrival Flight -->
                                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg mb-4">
                                    <h5 class="font-bold text-sm text-blue-800 dark:text-blue-300 mb-2">Arrival Flight ({{ item.date }})</h5>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Airline</label>
                                            <input type="text" v-model="form.flights[item.id].arrival_airline" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Flight Number</label>
                                            <input type="text" v-model="form.flights[item.id].arrival_number" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Arrival Time</label>
                                            <input type="time" v-model="form.flights[item.id].arrival_time" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Terminal</label>
                                            <select v-model="form.flights[item.id].arrival_terminal" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                                <option>T1</option>
                                                <option>T2</option>
                                                <option>T3</option>
                                                <option>T4</option>
                                                <option>FBO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Departure Flight (If Round Trip) -->
                                <div v-if="item.return_date" class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                                    <h5 class="font-bold text-sm text-orange-800 dark:text-orange-300 mb-2">Departure Flight ({{ item.return_date }})</h5>
                                     <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Airline</label>
                                            <input type="text" v-model="form.flights[item.id].departure_airline" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Flight Number</label>
                                            <input type="text" v-model="form.flights[item.id].departure_number" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Departure Time</label>
                                            <input type="time" v-model="form.flights[item.id].departure_time" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Terminal</label>
                                            <select v-model="form.flights[item.id].departure_terminal" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
                                                <option>T1</option>
                                                <option>T2</option>
                                                <option>T3</option>
                                                <option>T4</option>
                                                <option>FBO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 italic">
                                        Return pickup will be calculated automatically based on flight time and zone distance.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="md:col-span-1">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700 sticky top-4">
                            <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-4">Summary</h3>
                            <!-- Items List -->
                            <div class="space-y-4 mb-6">
                                <div v-for="item in cart.items" :key="item.id" class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-300">{{ item.provider_service.name }} x{{ item.quantity }}</span>
                                    <span class="font-bold text-gray-900 dark:text-white">${{ item.price }}</span>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-6">
                                <div class="flex justify-between text-lg font-bold">
                                    <span class="text-gray-900 dark:text-white">Total</span>
                                    <span class="text-primary-600 dark:text-cyan-400">
                                        ${{ cart.items.reduce((acc, item) => acc + parseFloat(item.price), 0).toFixed(2) }}
                                    </span>
                                </div>
                            </div>

                            <button @click="submit" :disabled="form.processing" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-full font-bold shadow-lg transition duration-200 disabled:opacity-50">
                                Proceed to Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
