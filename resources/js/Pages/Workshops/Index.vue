<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    workshops: Array
});

const selectedWorkshop = ref(null);
const showModal = ref(false);

const form = useForm({
    service_id: '',
    name: '',
    company: '',
    email: '',
    phone: '',
    gender: '',
    preferred_date: '',
    preferred_time: '',
});

const openModal = (workshop) => {
    selectedWorkshop.value = workshop;
    form.service_id = workshop.id;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    form.post(route('workshops.meeting'), {
        onSuccess: () => {
            closeModal();
            // Toast handled by layout
        }
    });
};
</script>

<template>
    <Head title="Talleres y Conferencias" />
    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                     <h1 class="text-4xl font-serif font-bold text-secondary-900 dark:text-white mb-4">Talleres y Conferencias</h1>
                     <p class="text-lg text-secondary-600 dark:text-secondary-400 max-w-2xl mx-auto">Capacitaciones y pláticas especializadas para empresas, escuelas y grupos.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="workshop in workshops" :key="workshop.id" class="bg-white dark:bg-secondary-800 rounded-xl shadow-lg overflow-hidden flex flex-col">
                         <div class="h-48 bg-primary-100 dark:bg-secondary-700 flex items-center justify-center">
                             <!-- Placeholder or Image -->
                             <span class="text-4xl">🎓</span>
                         </div>
                         <div class="p-6 flex-1 flex flex-col">
                             <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-2">{{ workshop.title }}</h3>
                             <p class="text-secondary-600 dark:text-secondary-400 text-sm mb-4 flex-1">{{ workshop.description }}</p>
                             
                             <div class="mt-4 pt-4 border-t border-gray-100 dark:border-secondary-700">
                                 <button @click="openModal(workshop)" class="w-full py-2 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition">
                                     Agendar Reunión
                                 </button>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-secondary-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-secondary-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-secondary-900 dark:text-white mb-4" id="modal-title">
                            Agendar Reunión: {{ selectedWorkshop?.title }}
                        </h3>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300">Nombre Completo</label>
                                <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-secondary-700 dark:border-secondary-600 dark:text-white">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300">Empresa / Institución</label>
                                <input v-model="form.company" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-secondary-700 dark:border-secondary-600 dark:text-white">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300">Email</label>
                                    <input v-model="form.email" type="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-secondary-700 dark:border-secondary-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300">Teléfono</label>
                                    <input v-model="form.phone" type="tel" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-secondary-700 dark:border-secondary-600 dark:text-white">
                                </div>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300">Género</label>
                                <select v-model="form.gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-secondary-700 dark:border-secondary-600 dark:text-white">
                                    <option value="">Seleccionar</option>
                                    <option value="male">Masculino</option>
                                    <option value="female">Femenino</option>
                                    <option value="other">Otro</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300">Fecha Preferida</label>
                                    <input v-model="form.preferred_date" type="date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-secondary-700 dark:border-secondary-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300">Hora</label>
                                    <input v-model="form.preferred_time" type="time" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-secondary-700 dark:border-secondary-600 dark:text-white">
                                </div>
                            </div>

                            <div class="mt-5 sm:mt-6 flex justify-end space-x-3">
                                <button type="button" @click="closeModal" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">Cancelar</button>
                                <button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none sm:text-sm">
                                    Solicitar Reunión
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
