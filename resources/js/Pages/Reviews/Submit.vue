<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'; // Or a simpler Public Layout
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    token: String,
    customerName: String,
});

const form = useForm({
    rating: 5,
    content: '',
    reviewer_name: props.customerName,
});

const submit = () => {
    form.post(route('reviews.store', props.token));
};

const setRating = (val) => {
    form.rating = val;
};
</script>

<template>
    <Head title="Share your Experience" />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col justify-center items-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                How was your trip?
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                We'd love to hear your feedback.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <!-- Rating -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rating</label>
                        <div class="flex gap-2 mt-2 justify-center">
                            <button 
                                v-for="star in 5" 
                                :key="star" 
                                type="button"
                                @click="setRating(star)"
                                class="text-3xl focus:outline-none transition-colors duration-150"
                                :class="star <= form.rating ? 'text-yellow-400' : 'text-gray-300'"
                            >
                                ★
                            </button>
                        </div>
                        <InputError :message="form.errors.rating" class="mt-2" />
                    </div>

                    <!-- Content -->
                    <div>
                        <InputLabel for="content" value="Your Comments" />
                        <textarea
                            id="content"
                            v-model="form.content"
                            rows="4"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            placeholder="Tell us about your experience..."
                            required
                        ></textarea>
                         <InputError :message="form.errors.content" class="mt-2" />
                    </div>

                    <!-- Name -->
                    <div>
                        <InputLabel for="reviewer_name" value="Your Name" />
                        <TextInput
                            id="reviewer_name"
                            v-model="form.reviewer_name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                         <InputError :message="form.errors.reviewer_name" class="mt-2" />
                    </div>

                    <div>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="w-full justify-center">
                            Submit Review
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
