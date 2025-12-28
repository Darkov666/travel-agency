<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const searchForm = useForm({
    origin: '',
    destination: '',
    departureDate: '',
    returnDate: '',
    passengers: 1,
    tripType: 'roundtrip'
});

const submitSearch = () => {
    // Navigate to booking or results page
    console.log('Searching for:', searchForm.data());
    // Inertia.visit('/booking', { data: searchForm });
};
</script>

<template>
    <div class="w-full max-w-4xl mx-auto -mt-24 relative z-20 px-4">
        <div class="bg-white/90 dark:bg-secondary-800/90 backdrop-blur-md rounded-2xl shadow-xl p-6 border border-white/20">
            <!-- Trip Type Tabs -->
            <div class="flex space-x-6 mb-6">
                <label class="flex items-center space-x-2 cursor-pointer group">
                    <input type="radio" value="oneway" v-model="searchForm.tripType" class="form-radio text-primary-500 focus:ring-primary-500 h-5 w-5 border-gray-300">
                    <span class="text-secondary-700 dark:text-secondary-200 font-medium group-hover:text-primary-600 transition">One Way</span>
                </label>
                <label class="flex items-center space-x-2 cursor-pointer group">
                    <input type="radio" value="roundtrip" v-model="searchForm.tripType" class="form-radio text-primary-500 focus:ring-primary-500 h-5 w-5 border-gray-300">
                    <span class="text-secondary-700 dark:text-secondary-200 font-medium group-hover:text-primary-600 transition">Round Trip</span>
                </label>
            </div>

            <!-- Search Fields Grid -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                
                <!-- Origin -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-secondary-600 dark:text-secondary-300 mb-1">Pick-up Location</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <input type="text" v-model="searchForm.origin" placeholder="Airport, Hotel..." class="pl-10 block w-full rounded-lg border-secondary-300 bg-secondary-50 dark:bg-secondary-700 dark:border-secondary-600 focus:ring-primary-500 focus:border-primary-500 transition-shadow">
                    </div>
                </div>

                <!-- Destination -->
                <div class="md:col-span-3">
                    <label class="block text-sm font-semibold text-secondary-600 dark:text-secondary-300 mb-1">Drop-off Point</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-8a2 2 0 012-2h14a2 2 0 012 2v8" />
                            </svg>
                        </div>
                        <input type="text" v-model="searchForm.destination" placeholder="Hotel, Area..." class="pl-10 block w-full rounded-lg border-secondary-300 bg-secondary-50 dark:bg-secondary-700 dark:border-secondary-600 focus:ring-primary-500 focus:border-primary-500 transition-shadow">
                    </div>
                </div>

                <!-- Dates -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-secondary-600 dark:text-secondary-300 mb-1">Date</label>
                    <input type="date" v-model="searchForm.departureDate" class="block w-full rounded-lg border-secondary-300 bg-secondary-50 dark:bg-secondary-700 dark:border-secondary-600 focus:ring-primary-500 focus:border-primary-500 transition-shadow text-sm">
                </div>

                <!-- Passengers -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-secondary-600 dark:text-secondary-300 mb-1">Passengers</label>
                    <select v-model="searchForm.passengers" class="block w-full rounded-lg border-secondary-300 bg-secondary-50 dark:bg-secondary-700 dark:border-secondary-600 focus:ring-primary-500 focus:border-primary-500 transition-shadow">
                        <option v-for="n in 12" :key="n" :value="n">{{ n }} Pax</option>
                    </select>
                </div>

                <!-- Search Button -->
                <div class="md:col-span-2">
                    <button @click="submitSearch" class="w-full bg-primary-600 hover:bg-primary-500 text-white font-bold py-2.5 px-4 rounded-lg shadow-lg hover:shadow-primary-500/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
