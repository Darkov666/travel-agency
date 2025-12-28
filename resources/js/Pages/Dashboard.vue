<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { wTrans } from 'laravel-vue-i18n';

const props = defineProps({
    appointments: Array,
});

const t = wTrans;

const formatDate = (dateString, timeString) => {
    if (!dateString) return 'Fecha Pendiente';
    // If separate columns or datetime
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-MX', {
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric'
    }).format(date);
};

const getStatusColor = (status, paymentStatus, scheduledAt) => {
    if (!scheduledAt) return 'bg-yellow-100 text-yellow-800'; // Open Date
    if (status === 'confirmed') return 'bg-green-100 text-green-800';
    if (status === 'cancelled') return 'bg-red-100 text-red-800';
    return 'bg-blue-100 text-blue-800'; // Pending
};

const getStatusLabel = (status, scheduledAt) => {
    if (!scheduledAt) return 'Por Agendar';
    if (status === 'confirmed') return t('booking.confirmed') || 'Confirmada';
    if (status === 'cancelled') return t('booking.cancelled') || 'Cancelada';
    return t('booking.pending') || 'Pendiente';
};
</script>

<template>
    <Head title="Mi Panel" />

    <MainLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ t('Dashboard') || 'Mi Panel' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-secondary-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Mis Citas</h3>

                    <div v-if="appointments.length === 0" class="text-center py-10 text-gray-500">
                        No tienes citas agendadas aún.
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="appt in appointments" :key="appt.id" class="border border-gray-200 dark:border-secondary-700 rounded-lg p-4 flex flex-col md:flex-row justify-between items-center transition hover:shadow-md">
                            <div class="mb-4 md:mb-0">
                                <h4 class="font-bold text-lg text-primary-700 dark:text-primary-300">{{ appt.service?.title || 'Servicio' }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-semibold">Fecha:</span> {{ formatDate(appt.scheduled_at) }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-semibold">Tipo:</span> {{ appt.session_type === 'online' ? 'Online' : 'Presencial' }}
                                </p>
                            </div>

                            <div class="flex flex-col items-end space-y-2">
                                <span :class="['px-3 py-1 rounded-full text-xs font-semibold uppercase', getStatusColor(appt.status, appt.payment_status, appt.scheduled_at)]">
                                    {{ getStatusLabel(appt.status, appt.scheduled_at) }}
                                </span>

                                <!-- Action Button for Open Dates -->
                                <Link 
                                    v-if="!appt.scheduled_at && appt.status !== 'cancelled'" 
                                    :href="route('booking.date', appt.id)" 
                                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:ring focus:ring-primary-300 disabled:opacity-25 transition"
                                >
                                    Agendar Ahora
                                </Link>
                                
                                <div v-if="appt.payment_status === 'pending' && appt.payment_method === 'transfer'" class="text-xs text-orange-500">
                                    Esperando pago
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
