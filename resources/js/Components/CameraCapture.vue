<script setup>
import { ref, onUnmounted, nextTick } from 'vue';

const emit = defineEmits(['capture', 'close']);

const videoRef = ref(null);
const canvasRef = ref(null);
const stream = ref(null);
const isCameraOpen = ref(false);
const error = ref(null);

const startCamera = async () => {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        error.value = "Tu navegador no soporta el acceso a la cámara. Por favor, usa la opción de subir foto.";
        return;
    }

    try {
        stream.value = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        isCameraOpen.value = true;
        error.value = null;
        
        await nextTick();
        
        if (videoRef.value) {
            videoRef.value.srcObject = stream.value;
        }
    } catch (err) {
        console.error("Error accessing camera:", err);
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
             error.value = "Permiso denegado. Por favor permite el acceso a la cámara en tu navegador.";
        } else {
             error.value = "No se pudo acceder a la cámara. Asegúrate de estar en un sitio seguro (HTTPS) o usa localhost.";
        }
    }
};

const stopCamera = () => {
    if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop());
        stream.value = null;
        isCameraOpen.value = false;
    }
};

const close = () => {
    stopCamera();
    emit('close');
};

const capturePhoto = () => {
    console.log('Capture photo clicked');
    if (videoRef.value && canvasRef.value) {
        console.log('Video and canvas refs available');
        const context = canvasRef.value.getContext('2d');
        canvasRef.value.width = videoRef.value.videoWidth;
        canvasRef.value.height = videoRef.value.videoHeight;
        context.drawImage(videoRef.value, 0, 0, canvasRef.value.width, canvasRef.value.height);
        
        const dataUrl = canvasRef.value.toDataURL('image/jpeg');
        console.log('Photo captured, emitting event');
        emit('capture', dataUrl);
        stopCamera();
    } else {
        console.error('Video or canvas ref missing', { video: videoRef.value, canvas: canvasRef.value });
    }
};

onUnmounted(() => {
    stopCamera();
});
</script>

<template>
    <div class="camera-capture">
        <div v-if="error" class="text-red-500 mb-2 text-sm">{{ error }}</div>
        
        <div v-if="!isCameraOpen" class="text-center">
            <button 
                type="button"
                @click="startCamera" 
                class="px-4 py-2 bg-secondary-200 dark:bg-secondary-700 text-secondary-700 dark:text-secondary-200 rounded-lg hover:bg-secondary-300 dark:hover:bg-secondary-600 transition flex items-center justify-center mx-auto"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Abrir Cámara
            </button>
        </div>

        <div v-else class="relative rounded-lg overflow-hidden bg-black">
            <video ref="videoRef" autoplay playsinline class="w-full h-64 object-cover"></video>
            <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-4 z-10">
                <button 
                    type="button"
                    @click="capturePhoto" 
                    class="p-3 bg-white rounded-full shadow-lg hover:bg-gray-100 transition border-4 border-gray-200"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
                <button 
                    type="button"
                    @click="close" 
                    class="p-3 bg-red-500 rounded-full shadow-lg hover:bg-red-600 transition text-white border-4 border-red-300"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <canvas ref="canvasRef" class="hidden"></canvas>
    </div>
</template>
