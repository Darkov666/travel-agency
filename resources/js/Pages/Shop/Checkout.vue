<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { wTrans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import ClientFormFields from '@/Components/ClientFormFields.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    cart: Object,
    appointments: Object, // { item_id: { date, time } }
});

const t = wTrans;
const form = useForm({
    payment_method: '',
    name: '',
    surname: '',
    email: '',
    phone: '',
    gender: '',
    custom_gender: '',
    photo: null,
    session_type: 'online',
});

const total = computed(() => {
    return props.cart?.items.reduce((sum, item) => sum + (item.service.price_mxn * item.quantity), 0) || 0;
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    }).format(price);
};

const submit = () => {
    if (form.gender === 'other' && form.custom_gender) {
        form.gender = form.custom_gender;
    }

    if (!form.gender) {
        Swal.fire({
            icon: 'warning',
            title: t('booking.missing_gender') || 'Género requerido',
            text: t('booking.select_gender_desc') || 'Por favor selecciona un género.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }

    const phoneRegex = /^\d{10}$/;
    if (!phoneRegex.test(form.phone)) {
        Swal.fire({
            icon: 'warning',
            title: t('booking.invalid_phone') || 'Teléfono inválido',
            text: t('booking.invalid_phone_desc') || 'El teléfono debe tener 10 dígitos numéricos.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }

    if (!form.photo && !props.cart.user_id) { // Only require photo if not logged in (or we could require it always to update)
       // Actually, requirements say "recabar la misma informacion", implying always.
       // But if user is logged in, they might already have a photo.
       // Let's assume we require it if it's a new registration or purely consistent with IndividualForm.
       // IndividualForm requires it. I'll require it here too for consistency, unless user is logged in?
       // Let's make it required if !user. If user exists, we assume they have one or we don't block.
       // Wait, the form fields are shown only if !user in the original code? 
       // Original had `v-if="!$page.props.auth.user"`. I should keep that check? 
       // User said "recabar la misma informacion que recabamos cuando comprasmo en booknow".
       // If I am logged in, `IndividualForm` might pre-fill?
       // Let's check IndividualForm. It doesn't seem to have v-if user check for fields. It asks everyone.
       // So I should ask everyone.
       // BUT, checking the original Checkout.vue again...
       // Line 72: `<div v-if="!$page.props.auth.user"` implies it only asked guests.
       // I should probably remove that v-if and ask everyone if we want to be consistent with "Booking" flow which asks for specific details for *this* appointment?
       // Actually, Booking flow (IndividualForm) creates a NEW user or updates?
       // It seems to post to `booking.store`.
       // Let's remove the `v-if="!$page.props.auth.user"` to force data collection as per request "recabar la misma informacion".
    }
    
    // Explicit check on photo
    if (!form.photo && !props.auth?.user?.profile_photo_path) {
         // If we strictly follow "recabar la misma informacion", we need photo.
         // Let's enforce it if form.photo is empty.
         Swal.fire({
            icon: 'warning',
            title: t('booking.photo_required') || 'Foto requerida',
            text: t('booking.photo_required_desc') || 'Por favor toma o sube una foto personal para continuar.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }

    form.post(route('checkout.store'), {
        forceFormData: true,
        onSuccess: () => {
            // Redirect handled by backend
        }
    });
};
</script>

<template>
    <Head title="Checkout" />
    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-secondary-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-6">
                            Finalizar Compra
                        </h2>

                        <!-- Summary -->
                        <div class="bg-primary-50 dark:bg-secondary-700 p-6 rounded-lg mb-8">
                            <h3 class="text-lg font-semibold text-primary-800 dark:text-primary-200 mb-4">{{ t('booking.summary') }}</h3>
                            <div class="space-y-2 text-secondary-700 dark:text-secondary-300">
                                <div v-for="item in cart.items" :key="item.id" class="flex flex-col border-b border-gray-100 dark:border-secondary-600 pb-2 mb-2 last:border-0 last:pb-0 last:mb-0">
                                    <div class="flex justify-between">
                                        <span>{{ item.quantity }}x {{ item.service.title }}</span>
                                        <span class="font-medium">{{ formatPrice(item.service.price_mxn * item.quantity) }}</span>
                                    </div>
                                    <div v-if="appointments && appointments[item.id]" class="text-sm text-primary-600 dark:text-primary-400 mt-1">
                                        📅 {{ appointments[item.id].date }} a las {{ appointments[item.id].time }}
                                    </div>
                                </div>
                                <div class="flex justify-between text-lg font-bold text-primary-700 dark:text-primary-300 pt-2 border-t border-primary-100 dark:border-secondary-600">
                                    <span>{{ t('booking.total') }}:</span>
                                    <span>{{ formatPrice(total) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Registration / Contact Info -->
                        <!-- Display for everyone to ensure data collection -->
                         <div class="bg-gray-50 dark:bg-secondary-700 p-6 rounded-lg mb-8">
                            <h3 class="text-lg font-semibold text-secondary-900 dark:text-white mb-4">{{ t('Contact Information') }}</h3>
                            <ClientFormFields :form="form" />
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <h3 class="text-lg font-medium text-secondary-900 dark:text-white mb-4">{{ t('booking.select_payment_method') }}</h3>
                            
                            <div class="space-y-4">
                                <!-- PayPal -->
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-700 transition" :class="{'border-primary-500 ring-1 ring-primary-500': form.payment_method === 'paypal', 'border-gray-200 dark:border-secondary-600': form.payment_method !== 'paypal'}">
                                    <input type="radio" v-model="form.payment_method" value="paypal" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" />
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-secondary-900 dark:text-white">PayPal</span>
                                        <span class="block text-sm text-secondary-500 dark:text-secondary-400">Pagar con PayPal (Simulación)</span>
                                    </div>
                                </label>

                                <!-- Transfer -->
                                <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-secondary-700 transition" :class="{'border-primary-500 ring-1 ring-primary-500': form.payment_method === 'transfer', 'border-gray-200 dark:border-secondary-600': form.payment_method !== 'transfer'}">
                                    <input type="radio" v-model="form.payment_method" value="transfer" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300" />
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-secondary-900 dark:text-white">{{ t('booking.bank_transfer') }}</span>
                                        <span class="block text-sm text-secondary-500 dark:text-secondary-400">{{ t('booking.bank_transfer_desc') }}</span>
                                    </div>
                                </label>
                            </div>

                            <div class="pt-6">
                                <button type="submit" :disabled="form.processing || !form.payment_method" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition disabled:opacity-50">
                                    Confirmar Compra
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
