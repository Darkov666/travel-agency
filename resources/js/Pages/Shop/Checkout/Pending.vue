<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    reservation: Object,
});

const whatsappLink = computed(() => {
    const phone = '529981234567'; // Replace with real admin phone
    const text = `Hello, I have made a transfer for Booking Ref: ${props.reservation.booking_ref}. Here is the proof of payment.`;
    return `https://wa.me/${phone}?text=${encodeURIComponent(text)}`;
});

import { computed } from 'vue';
</script>

<template>
    <Head title="Payment Pending" />

    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
             <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                 <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                     <div class="bg-orange-500 p-6 text-center">
                         <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 mb-4">
                             <svg class="h-8 w-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                             </svg>
                         </div>
                         <h2 class="text-2xl font-bold text-white">Payment Pending</h2>
                         <p class="text-orange-100 mt-2">Ref: {{ reservation.booking_ref }}</p>
                     </div>
                     
                     <div class="p-8 space-y-6">
                         <div class="text-center text-gray-600 dark:text-gray-300">
                             <p>Your reservation has been created but we are waiting for payment proof to confirm it.</p>
                             <p class="mt-2 text-sm">Please make a transfer of <span class="font-bold text-gray-900 dark:text-white">${{ reservation.total_amount * 0.20 }} (Deposit)</span> or full amount to:</p>
                         </div>

                         <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-6 border border-gray-200 dark:border-gray-600">
                             <h3 class="font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-600">Bank Details</h3>
                             <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                 <div class="sm:col-span-1">
                                     <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Bank</dt>
                                     <dd class="mt-1 text-sm text-gray-900 dark:text-white">BBVA</dd>
                                 </div>
                                 <div class="sm:col-span-1">
                                     <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Account Holder</dt>
                                     <dd class="mt-1 text-sm text-gray-900 dark:text-white">Travel Agency SA</dd>
                                 </div>
                                 <div class="sm:col-span-2">
                                     <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">CLABE</dt>
                                     <dd class="mt-1 text-med font-mono text-gray-900 dark:text-white">012345678901234567</dd>
                                 </div>
                             </dl>
                         </div>

                         <div class="text-center space-y-4">
                             <p class="text-sm text-gray-500 mb-4">Once transferred, please send the proof via WhatsApp to confirm your booking.</p>
                             
                             <a :href="whatsappLink" target="_blank" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 w-full sm:w-auto">
                                 <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                 Send Proof via WhatsApp
                             </a>
                         </div>
                     </div>
                 </div>
                 
                 <div class="text-center mt-8">
                     <Link href="/" class="text-primary-600 hover:text-primary-800 font-medium">Return to Home</Link>
                 </div>
             </div>
        </div>
    </MainLayout>
</template>
