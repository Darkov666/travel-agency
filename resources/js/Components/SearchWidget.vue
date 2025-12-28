<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    type: 'round_trip', // one_way, round_trip
    destination: '',
    date: '',
    pax: 2,
});

const zones = [
    'Cancun Hotel Zone',
    'Playa Mujeres',
    'Puerto Morelos',
    'Playa del Carmen',
    'Puerto Aventuras',
    'Akumal',
    'Tulum'
];

const submit = () => {
    form.get(route('search'), {
        preserveScroll: true,
        onSuccess: () => console.log('Search successful'),
    });
};
</script>

<template>
    <div class="relative z-30 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 md:p-8 transition-colors duration-300">
            <h2 class="text-2xl font-serif font-bold text-secondary-900 dark:text-white mb-6 text-center md:text-left">
                Find your perfect transfer
            </h2>
            
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Transfer Type -->
                    <div class="col-span-1 md:col-span-2 lg:col-span-1">
                        <label class="block text-sm font-medium text-secondary-700 dark:text-gray-200 mb-1">Type</label>
                         <select v-model="form.type" class="w-full rounded-lg border-gray-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                            <option value="one_way">One Way</option>
                            <option value="round_trip">Round Trip</option>
                        </select>
                    </div>

                    <!-- Destination -->
                    <div class="col-span-1 md:col-span-2 lg:col-span-1">
                        <label class="block text-sm font-medium text-secondary-700 dark:text-gray-200 mb-1">Destination</label>
                        <select v-model="form.destination" class="w-full rounded-lg border-gray-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                             <option value="" disabled>Select Zone</option>
                             <option v-for="zone in zones" :key="zone" :value="zone">{{ zone }}</option>
                        </select>
                    </div>

                    <!-- Date -->
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-secondary-700 dark:text-gray-200 mb-1">Arrival Date</label>
                        <input type="date" v-model="form.date" class="w-full rounded-lg border-gray-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    <!-- Passengers -->
                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-secondary-700 dark:text-gray-200 mb-1">Passengers</label>
                        <input type="number" min="1" max="20" v-model="form.pax" class="w-full rounded-lg border-gray-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>

                <div class="flex justify-center md:justify-end mt-6">
                    <button type="submit" class="w-full md:w-auto px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-full shadow-lg transform hover:-translate-y-0.5 transition duration-200">
                        Search Transfers
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
