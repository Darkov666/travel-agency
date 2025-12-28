<script setup>
import { ref, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    appointment: Object,
    availableSlots: Array,
    currentDate: String,
});

const form = useForm({
    date: props.currentDate || '',
    time: '',
});

const selectedDate = ref(props.currentDate || '');
const selectedTime = ref('');

const submit = () => {
    console.log('Submit triggered. Selected Time:', selectedTime.value);
    
    if (!selectedTime.value) {
        alert('Por favor selecciona una hora.');
        return;
    }

    form.date = selectedDate.value;
    form.time = selectedTime.value;

    console.log('Submitting booking date:', form.date, form.time, 'Appointment ID:', props.appointment?.id);

    if (!props.appointment?.id) {
         console.error('Missing appointment ID');
         alert('Error: No booking ID found.');
         return;
    }

    form.post(`/booking/${props.appointment.id}/date`, {
        onSuccess: () => {
            console.log('Date confirmation successful');
        },
        onError: (errors) => {
            console.error('Booking errors:', errors);
            // alert('Hubo un error al guardar la fecha. Por favor revisa la consola.');
        },
        onFinish: () => console.log('Booking submission finished')
    });
};

// Initialize with props
onMounted(() => {
    // If we had a date picker, we'd use it. 
    // For now, let's just display the slots provided by the backend.
});
</script>

<template>
    <MainLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-secondary-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-6">
                        Selecciona una Fecha y Hora
                    </h2>

                    <div class="mb-6">
                        <p class="text-secondary-600 dark:text-secondary-300">
                            Reserva para: <strong>{{ appointment.customer_name }}</strong>
                        </p>
                        <p class="text-secondary-600 dark:text-secondary-300">
                            Servicio: <strong>{{ appointment.service?.title }}</strong>
                        </p>
                    </div>

                    <!-- Simple Date Picker (Native) -->
                    <div class="mb-6">
                        <label class="block text-secondary-700 dark:text-secondary-300 mb-2">Fecha</label>
                        <input 
                            type="date" 
                            v-model="selectedDate"
                            @change="$inertia.get(`/booking/${appointment.id}/date`, { date: selectedDate }, { preserveState: true, preserveScroll: true })"
                            class="form-input rounded-md shadow-sm mt-1 block w-full dark:bg-secondary-900 dark:text-white"
                        />
                    </div>

                    <div v-if="availableSlots.length > 0">
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <button 
                                v-for="slot in availableSlots" 
                                :key="slot.time"
                                @click="selectedTime = slot.time"
                                :disabled="!slot.available"
                                :class="{
                                    'opacity-50 cursor-not-allowed bg-gray-300 text-gray-500': !slot.available, 
                                    'bg-white text-primary-600 border border-primary-600 hover:bg-primary-50': slot.available && selectedTime !== slot.time,
                                    'bg-primary-600 text-white border border-primary-600': selectedTime === slot.time
                                }"
                                class="py-2 px-4 rounded shadow transition font-medium"
                            >
                                {{ slot.time }}
                            </button>
                        </div>

                        <div class="flex justify-end">
                            <button 
                                @click="submit" 
                                :disabled="!selectedTime"
                                :class="{'opacity-50 cursor-not-allowed': !selectedTime, 'hover:bg-primary-700': selectedTime}"
                                class="px-6 py-3 bg-primary-600 text-white rounded-lg font-bold shadow-lg transition"
                            >
                                {{ $t('booking.confirm_date') }}
                            </button>
                        </div>
                    </div>
                    <div v-else class="text-center text-secondary-500">
                        {{ $t('booking.no_slots') }}
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
