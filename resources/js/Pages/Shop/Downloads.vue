<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

defineProps({
    appointment: Object,
    files: Array,
});
</script>

<template>
    <Head title="Descargas" />
    <MainLayout>
        <div class="py-12 bg-gray-50 dark:bg-secondary-900 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-secondary-800 rounded-xl shadow-lg p-8">
                    <div class="text-center mb-8">
                        <div class="h-16 w-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-secondary-900 dark:text-white mb-2">¡Todo listo!</h1>
                        <p class="text-secondary-600 dark:text-secondary-400">
                            Gracias por confirmar tu cita para <strong>{{ appointment.service.title }}</strong>.
                        </p>
                    </div>

                    <div class="border-t border-gray-200 dark:border-secondary-700 pt-8">
                        <h2 class="text-xl font-semibold text-secondary-900 dark:text-white mb-6">Tus Archivos y Recursos</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div v-for="file in files" :key="file.name" class="p-4 border border-gray-200 dark:border-secondary-600 rounded-lg flex items-center justify-between hover:bg-gray-50 dark:hover:bg-secondary-700 transition">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="font-medium text-secondary-900 dark:text-white">{{ file.name }}</span>
                                </div>
                                <a :href="file.url" class="text-primary-600 hover:text-primary-700 font-semibold text-sm">Descargar</a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 text-center">
                        <Link href="/" class="text-primary-600 hover:text-primary-700 font-medium">Volver al Inicio</Link>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
