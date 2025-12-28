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
    name: '', // Primary contact
    surname: '',
    email: '',
    phone: '',
    gender: '',
    custom_gender: '',
    photo: null,
    session_type: 'online',
    service_id: props.service?.id || null,
    participants: [
        { name: '', surname: '', gender: '', custom_gender: '' } // Initial participant
    ]
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

const addParticipant = () => {
    form.participants.push({ name: '', surname: '', gender: '', custom_gender: '' });
};

const removeParticipant = (index) => {
    form.participants.splice(index, 1);
};

const submit = () => {
    if (form.gender === 'other' && form.custom_gender) {
        form.gender = form.custom_gender;
    }
    form.participants.forEach(p => {
        if (p.gender === 'other' && p.custom_gender) {
            p.gender = p.custom_gender;
        }
    });

    // Validate all participants have gender
    for (const [index, p] of form.participants.entries()) {
        if (!p.gender) {
            Swal.fire({
                icon: 'warning',
                title: t('booking.missing_gender') || 'Género requerido',
                text: `${t('booking.participant')} ${index + 1}: ${t('booking.select_gender_desc') || 'Por favor selecciona un género.'}`,
                confirmButtonColor: '#E99FA0',
            });
            return;
        }
    }

    // Main Contact Validation
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

    form.post('/booking/group', {
        onSuccess: () => {
            // Redirect handled by backend
        }
    });
};
</script>

<template>
    <Head :title="t('booking.group_title')" />
    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-secondary-800 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-6">
                            {{ t('booking.group_registration') }}
                        </h2>
                        
                        <div v-if="service" class="mb-6 p-4 bg-primary-50 dark:bg-secondary-700 rounded-lg">
                            <p class="text-sm text-secondary-500 dark:text-secondary-300">{{ t('booking.selected_service') }}:</p>
                            <p class="text-lg font-semibold text-primary-700 dark:text-primary-300">{{ service.title }}</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Primary Contact -->
                            <div class="bg-gray-50 dark:bg-secondary-700/50 p-6 rounded-lg border border-gray-100 dark:border-secondary-700">
                                <!-- Session Type -->
                                <div class="mb-6">
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

                                <h3 class="text-lg font-medium text-secondary-900 dark:text-white mb-4">{{ t('booking.primary_contact') }}</h3>
                                
                                <!-- Profile Photo Section -->
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
                                    </div>
                                    
                                    <CameraModal :show="showCamera" @close="showCamera = false" @capture="handlePhotoCapture" />
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
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.gender') }}</label>
                                        <select v-model="form.gender" class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500 mb-2">
                                            <option value="">{{ t('booking.select_gender') }}</option>
                                            <option value="male">{{ t('booking.male') }}</option>
                                            <option value="female">{{ t('booking.female') }}</option>
                                            <option value="non_binary">{{ t('booking.non_binary') }}</option>
                                            <option value="other">{{ t('booking.other') }}</option>
                                        </select>
                                        <input v-if="form.gender === 'other'" v-model="form.custom_gender" type="text" :placeholder="t('booking.specify_gender')" class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                    </div>
                                </div>
                            </div>

                            <!-- Participants -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-secondary-900 dark:text-white">{{ t('booking.participants') }}</h3>
                                    <button type="button" @click="addParticipant" class="text-sm text-primary-600 hover:text-primary-700 font-medium flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        {{ t('booking.add_participant') }}
                                    </button>
                                </div>

                                <div v-for="(participant, index) in form.participants" :key="index" class="bg-gray-50 dark:bg-secondary-700/50 p-6 rounded-lg border border-gray-100 dark:border-secondary-700 relative">
                                    <button v-if="form.participants.length > 1" type="button" @click="removeParticipant(index)" class="absolute top-4 right-4 text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    
                                    <h4 class="text-sm font-medium text-secondary-500 dark:text-secondary-400 mb-4">{{ t('booking.participant') }} {{ index + 1 }}</h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.name') }}</label>
                                            <input v-model="participant.name" type="text" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.surname') }}</label>
                                            <input v-model="participant.surname" type="text" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">{{ t('booking.gender') }}</label>
                                            <select v-model="participant.gender" required class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500 mb-2">
                                                <option value="">{{ t('booking.select_gender') }}</option>
                                                <option value="male">{{ t('booking.male') }}</option>
                                                <option value="female">{{ t('booking.female') }}</option>
                                                <option value="non_binary">{{ t('booking.non_binary') }}</option>
                                                <option value="other">{{ t('booking.other') }}</option>
                                            </select>
                                            <input v-if="participant.gender === 'other'" v-model="participant.custom_gender" type="text" required :placeholder="t('booking.specify_gender')" class="w-full rounded-lg border-secondary-300 dark:border-secondary-600 dark:bg-secondary-700 dark:text-white focus:border-primary-500 focus:ring-primary-500" />
                                        </div>
                                    </div>
                                </div>
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
