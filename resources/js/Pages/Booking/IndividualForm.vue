<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import CameraModal from '@/Components/CameraModal.vue';
import { wTrans } from 'laravel-vue-i18n';
import Swal from 'sweetalert2';

const props = defineProps({
    service: Object,
});

const t = wTrans;
const form = useForm({
    name: '',
    surname: '', // Added surname as requested
    email: '',
    phone: '',
    gender: '',
    custom_gender: '',
    photo: null,
    session_type: 'online', // Default
    service_id: props.service?.id || null,
});

const photoPreview = ref(null);
const showCamera = ref(false);

const handlePhotoCapture = (dataUrl) => {
    photoPreview.value = dataUrl;
    // Convert DataURL to File object for upload
    fetch(dataUrl)
        .then(res => res.blob())
        .then(blob => {
            const file = new File([blob], "profile_photo.jpg", { type: "image/jpeg" });
            form.photo = file;
        });
    showCamera.value = false;
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.photo = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    if (form.gender === 'other' && form.custom_gender) {
        form.gender = form.custom_gender;
    }

    // Gender validation
    if (!form.gender) {
        Swal.fire({
            icon: 'warning',
            title: t('booking.missing_gender') || 'Género requerido',
            text: t('booking.select_gender_desc') || 'Por favor selecciona un género.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }

    // Phone validation (10 digits)
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

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(form.email)) {
        Swal.fire({
            icon: 'warning',
            title: t('booking.invalid_email') || 'Correo inválido',
            text: t('booking.invalid_email_desc') || 'Por favor ingresa un correo electrónico válido.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }

    if (!form.photo) {
        Swal.fire({
            icon: 'warning',
            title: t('booking.photo_required') || 'Foto requerida',
            text: t('booking.photo_required_desc') || 'Por favor toma o sube una foto personal para continuar.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }
    form.post('/booking/individual', {
        onSuccess: () => {
            // Redirect handled by backend (to payment or calendar)
        }
    });
};
</script>

<template>
    <Head :title="t('booking.individual_title')" />
    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-secondary-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-6">
                            {{ t('booking.individual_registration') }}
                        </h2>
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Photo Section -->
                            <div class="space-y-4">
                                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300">
                                    {{ t('booking.profile_photo') }}
                                </label>
                                
                                <div class="flex flex-col items-center space-y-4">
                                    <div v-if="photoPreview" class="w-32 h-32 rounded-full overflow-hidden border-4 border-primary-100 dark:border-secondary-600">
                                        <img :src="photoPreview" alt="Profile Preview" class="w-full h-full object-cover" />
                                    </div>
                                    <div v-else class="w-32 h-32 rounded-full bg-secondary-100 dark:bg-secondary-700 flex items-center justify-center text-secondary-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>

                                    <div class="flex space-x-4">
                                        <button 
                                            type="button" 
                                            @click="showCamera = true"
                                            class="px-4 py-2 bg-secondary-200 dark:bg-secondary-700 text-secondary-700 dark:text-secondary-200 rounded-lg hover:bg-secondary-300 dark:hover:bg-secondary-600 transition text-sm"
                                        >
                                            {{ t('booking.take_photo') }}
                                        </button>
                                        <label class="px-4 py-2 bg-secondary-200 dark:bg-secondary-700 text-secondary-700 dark:text-secondary-200 rounded-lg hover:bg-secondary-300 dark:hover:bg-secondary-600 transition text-sm cursor-pointer">
                                            {{ t('booking.upload_photo') }}
                                            <input type="file" @change="handleFileUpload" accept="image/*" class="hidden" />
                                        </label>
                                    </div>

                                    <CameraModal :show="showCamera" @close="showCamera = false" @capture="handlePhotoCapture" />
                                </div>
                            </div>

                            <!-- Personal Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.name') }}</label>
                                    <input v-model="form.name" type="text" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.surname') }}</label>
                                    <input v-model="form.surname" type="text" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.email') }}</label>
                                    <input v-model="form.email" type="email" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.phone') }}</label>
                                    <input v-model="form.phone" type="tel" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                </div>
                            </div>

                            <!-- Session Type -->
                            <div>
                                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.session_type') || 'Tipo de Sesión' }}</label>
                                <div class="flex space-x-4 mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.session_type" value="online" class="form-radio text-primary-600 focus:ring-primary-500 h-4 w-4">
                                        <span class="ml-2 text-secondary-700 dark:text-secondary-300">{{ t('booking.online_session') || 'Online (Google Meet)' }}</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.session_type" value="in_person" class="form-radio text-primary-600 focus:ring-primary-500 h-4 w-4">
                                        <span class="ml-2 text-secondary-700 dark:text-secondary-300">{{ t('booking.in_person_session') || 'Presencial' }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Gender -->
                            <div>
                                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.gender') }}</label>
                                <select v-model="form.gender" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500 mb-2">
                                    <option value="">{{ t('booking.select_gender') }}</option>
                                    <option value="male">{{ t('booking.male') }}</option>
                                    <option value="female">{{ t('booking.female') }}</option>
                                    <option value="non_binary">{{ t('booking.non_binary') }}</option>
                                    <option value="other">{{ t('booking.other') }}</option>
                                </select>
                                <input v-if="form.gender === 'other'" v-model="form.custom_gender" type="text" required :placeholder="t('booking.specify_gender')" class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                            </div>

                            <div class="pt-6">
                                <button type="submit" :disabled="form.processing" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition disabled:opacity-50">
                                    {{ t('booking.continue_to_calendar') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
