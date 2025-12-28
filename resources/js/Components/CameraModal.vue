<script setup>
import CameraCapture from '@/Components/CameraCapture.vue';

defineProps({
    show: Boolean,
});

const emit = defineEmits(['close', 'capture']);

const close = () => {
    emit('close');
};

const handleCapture = (dataUrl) => {
    emit('capture', dataUrl);
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" @click="close"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white dark:bg-secondary-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <div class="bg-white dark:bg-secondary-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4 text-center">
                        Tomar Foto
                    </h3>
                    <CameraCapture @capture="handleCapture" @close="close" />
                </div>
            </div>
        </div>
    </div>
</template>
