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
const showCamera = ref(false);
const photoPreview = ref(null);

const form = useForm({
    name: '',
    surname: '',
    email: '',
    phone: '',
    organization_type: '',
    organization_other: '',
    photo: null,
    service_id: props.service?.id || null,
});

const handlePhotoCapture = (dataUrl) => {
    photoPreview.value = dataUrl;
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
        photoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    // Basic Validation
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

    if (!form.photo) {
        Swal.fire({
            icon: 'warning',
            title: t('booking.photo_required') || 'Foto requerida',
            text: t('booking.photo_required_desc') || 'Por favor toma o sube una foto personal para continuar.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }
    
    // Organization Validation
    if (!form.organization_type) {
         Swal.fire({
            icon: 'warning',
            title: 'Organización Requerida',
            text: 'Por favor seleccione el tipo de organización.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }
    
    if (form.organization_type === 'Otros' && !form.organization_other.trim()) {
         Swal.fire({
            icon: 'warning',
            title: 'Especifique Organización',
            text: 'Por favor especifique el tipo de organización.',
            confirmButtonColor: '#E99FA0',
        });
        return;
    }

    form.post('/booking/workshop', {
        onSuccess: () => {
            // Redirect handled by backend
        }
    });
};
</script>

<template>
    <Head title="Reserva de Taller" />
    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-secondary-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-6">
                            Registro para Taller / Conferencia
                        </h2>
                        
                        <div v-if="service" class="mb-6 p-4 bg-primary-50 dark:bg-secondary-700 rounded-lg">
                            <p class="text-sm text-secondary-500 dark:text-secondary-300">{{ t('booking.selected_service') }}:</p>
                            <p class="text-lg font-semibold text-primary-700 dark:text-primary-300">{{ service.title }}</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            
                            <!-- Profile Photo -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">{{ t('booking.profile_photo') }}</label>
                                <div class="flex items-center space-x-6">
                                    <div class="shrink-0">
                                        <img v-if="photoPreview" :src="photoPreview" class="h-24 w-24 object-cover rounded-full border-2 border-primary-200" />
                                        <div v-else class="h-24 w-24 rounded-full bg-secondary-200 dark:bg-secondary-600 flex items-center justify-center">
                                            <svg class="h-10 w-10 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <button type="button" @click="showCamera = true" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition text-sm font-medium flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ t('booking.take_photo') }}
                                        </button>
                                        <div class="relative">
                                            <input type="file" accept="image/*" @change="handleFileUpload" class="hidden" id="photo-upload" />
                                            <label for="photo-upload" class="cursor-pointer px-4 py-2 border border-secondary-300 dark:border-secondary-600 rounded-lg hover:bg-secondary-50 dark:hover:bg-secondary-700 transition text-sm font-medium text-secondary-700 dark:text-secondary-300 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                {{ t('booking.upload_photo') }}
                                            </label>
                                        </div>
                                    </div>
                                    <CameraModal :show="showCamera" @close="showCamera = false" @capture="handlePhotoCapture" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.name') }}</label>
                                    <input v-model="form.name" type="text" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.surname') }}</label>
                                    <input v-model="form.surname" type="text" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.email') }}</label>
                                    <input v-model="form.email" type="email" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.phone') }}</label>
                                    <input v-model="form.phone" type="tel" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                </div>
                            </div>
                            
                            <!-- Organization Fields -->
                            <div class="border-t border-gray-200 dark:border-secondary-700 pt-6">
                                <h3 class="text-lg font-medium text-secondary-900 dark:text-white mb-4">Información de la Organización</h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Tipo de Organización</label>
                                        <select v-model="form.organization_type" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500">
                                            <option value="" disabled>Seleccione una opción</option>
                                            <option value="Empresa">Empresa</option>
                                            <option value="Institucion de gobierno">Institución de gobierno</option>
                                            <option value="Institucion no gubernamental">Institución no gubernamental</option>
                                            <option value="Institucion educativa">Institución educativa</option>
                                            <option value="Otros">Otros</option>
                                        </select>
                                    </div>
                                    <div v-if="form.organization_type === 'Otros'">
                                        <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Especifique Organización</label>
                                        <input v-model="form.organization_other" type="text" :required="form.organization_type === 'Otros'" placeholder="Describa su organización" class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                    </div>
                                </div>
                            </div>

                            <button type="submit" :disabled="form.processing" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition disabled:opacity-50">
                                {{ t('booking.continue_to_calendar') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
