<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const user = usePage().props.auth.user;
const photoPreview = ref(null);
const photoInput = ref(null);

const form = useForm({
    _method: 'POST', // Force POST for file upload (laravel method spoofing if needed, but inertia handles data object)
    name: user.name,
    email: user.email,
    password: '',
    password_confirmation: '',
    photo: null,
});

const updateProfileInformation = () => {
    if (photoInput.value && photoInput.value.files.length > 0) {
        form.photo = photoInput.value.files[0];
    }
    // Else: form.photo might have been set by the camera capture (capturePhoto)

    form.post(route('admin.profile.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => {
             photoPreview.value = null;
             clearPhotoFileInput();
        },
    });
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const videoRef = ref(null);
const canvasRef = ref(null);
const showCamera = ref(false);
const stream = ref(null);

const startCamera = async () => {
    showCamera.value = true;
    try {
        stream.value = await navigator.mediaDevices.getUserMedia({ video: true });
        // Use nextTick or simple timeout to ensure video element is rendered
        setTimeout(() => {
             if (videoRef.value) {
                videoRef.value.srcObject = stream.value;
            }
        }, 100);
    } catch (err) {
        console.error("Error accessing camera:", err);
        alert("Could not access camera. Please allow permissions.");
        showCamera.value = false;
    }
};

const stopCamera = () => {
    if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop());
        stream.value = null;
    }
    showCamera.value = false;
};

const capturePhoto = () => {
    if (!videoRef.value || !canvasRef.value) return;

    const context = canvasRef.value.getContext('2d');
    canvasRef.value.width = videoRef.value.videoWidth;
    canvasRef.value.height = videoRef.value.videoHeight;
    context.drawImage(videoRef.value, 0, 0, canvasRef.value.width, canvasRef.value.height);

    const dataUrl = canvasRef.value.toDataURL('image/jpeg');
    photoPreview.value = dataUrl;
    
    // Convert to file for form submission
    fetch(dataUrl)
        .then(res => res.blob())
        .then(blob => {
            const file = new File([blob], "camera-photo.jpg", { type: "image/jpeg" });
            form.photo = file;
            // Clear any traditional file input so it doesn't conflict
            clearPhotoFileInput();
        });

    stopCamera();
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};

</script>

<template>
    <Head title="Profile" />

    <AdminLayout>
        <template #header>
            Profile
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">Profile Information</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Update your account's profile information, email address, and avatar.
                        </p>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form @submit.prevent="updateProfileInformation">
                        <div class="shadow overflow-hidden sm:rounded-md">
                            <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6">
                                <div class="grid grid-cols-6 gap-6">
                                    <!-- Profile Photo -->
                                    <div class="col-span-6 sm:col-span-4">
                                        <!-- Profile Photo File Input -->
                                        <input type="file" class="hidden"
                                                    ref="photoInput"
                                                    @change="updatePhotoPreview">

                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Photo</label>

                                        <!-- Current Profile Photo -->
                                        <div class="mt-4" v-show="! photoPreview">
                                            <img :src="user.profile_photo_url || 'https://ui-avatars.com/api/?name='+user.name" alt="Current Profile Photo" class="rounded-full h-24 w-24 object-cover border-4 border-gray-200 dark:border-gray-700">
                                        </div>

                                        <!-- New Profile Photo Preview -->
                                        <div class="mt-4" v-show="photoPreview">
                                            <span class="block rounded-full w-24 h-24 bg-cover bg-no-repeat bg-center border-4 border-gray-200 dark:border-gray-700"
                                                  :style="'background-image: url(\'' + photoPreview + '\');'">
                                            </span>
                                        </div>

                                        <!-- Camera Preview -->
                                        <div v-if="showCamera" class="mt-4">
                                            <div class="relative w-64 h-64 bg-black rounded-lg overflow-hidden">
                                                <video ref="videoRef" autoplay playsinline class="w-full h-full object-cover"></video>
                                                <canvas ref="canvasRef" class="hidden"></canvas>
                                            </div>
                                            <div class="mt-2 flex space-x-2">
                                                 <button type="button" @click="capturePhoto" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Capture</button>
                                                 <button type="button" @click="stopCamera" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">Cancel</button>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex space-x-3" v-if="!showCamera">
                                            <button class="bg-white dark:bg-gray-700 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" type="button" @click.prevent="selectNewPhoto">
                                                Select File
                                            </button>

                                            <button class="bg-white dark:bg-gray-700 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" type="button" @click.prevent="startCamera">
                                                Take Photo
                                            </button>
                                        </div>
                                        <div v-if="form.errors.photo" class="text-red-500 text-sm mt-2">{{ form.errors.photo }}</div>
                                    </div>

                                    <!-- Name -->
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                        <input v-model="form.name" type="text" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <input v-model="form.email" type="email" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                                    </div>

                                    <!-- Password -->
                                     <div class="col-span-6 sm:col-span-4 border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Change Password (Optional)</h4>
                                     </div>

                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                                        <input v-model="form.password" type="password" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                                        <input v-model="form.password_confirmation" type="password" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right sm:px-6">
                                <button type="submit" :disabled="form.processing" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
