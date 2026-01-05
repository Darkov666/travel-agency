<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    token: String,
});

const form = useForm({
    rating: null,
    comments: '',
});

const submit = () => {
    form.post(route('feedback.website.store', props.token));
};
</script>

<template>
    <Head title="Website Feedback" />
    <MainLayout>
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                        How was your experience on our website?
                    </h2>
                    
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Rating -->
                        <div class="flex flex-col items-center">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rate your experience</label>
                            <div class="flex space-x-2">
                                <label v-for="star in 5" :key="star" class="cursor-pointer">
                                    <input type="radio" v-model="form.rating" :value="star" class="hidden">
                                    <svg class="w-10 h-10 transition-colors duration-200" :class="form.rating >= star ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-200'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </label>
                            </div>
                            <div v-if="form.errors.rating" class="text-red-500 text-xs mt-1">{{ form.errors.rating }}</div>
                        </div>

                        <!-- Comments -->
                        <div>
                            <label for="comments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Any additional comments or suggestions?</label>
                            <textarea v-model="form.comments" id="comments" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cyan-500 focus:ring-cyan-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Tell us what you liked or what we can improve..."></textarea>
                            <div v-if="form.errors.comments" class="text-red-500 text-xs mt-1">{{ form.errors.comments }}</div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" :disabled="form.processing" class="bg-cyan-600 text-white px-6 py-2 rounded-md hover:bg-cyan-700 transition font-medium disabled:opacity-50">
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
