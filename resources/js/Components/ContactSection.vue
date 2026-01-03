<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    message: '',
});

const successMessage = ref('');

const submit = () => {
    form.post(route('contact.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            successMessage.value = 'Message sent successfully! We will get back to you shortly.';
            setTimeout(() => {
                successMessage.value = '';
            }, 5000);
        },
    });
};
</script>

<template>
    <section class="py-20 bg-white transition-colors duration-300" id="contact">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-serif font-bold text-black mb-4">Contact Us</h2>
                <p class="text-lg text-black max-w-2xl mx-auto">
                    Have a question or need a custom quote? Send us a message and our team will assist you.
                </p>
            </div>

            <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 transform transition-all duration-300 hover:scale-[1.01]">
                <div v-if="successMessage" class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">
                    {{ successMessage }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-colors duration-300 px-4 py-3 outline-none"
                            placeholder="John Doe"
                        />
                        <div v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-colors duration-300 px-4 py-3 outline-none"
                            placeholder="john@example.com"
                        />
                        <div v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Message</label>
                        <textarea
                            id="message"
                            v-model="form.message"
                            rows="4"
                            required
                            class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 transition-colors duration-300 px-4 py-3 outline-none resize-none"
                            placeholder="How can we help you?"
                        ></textarea>
                        <div v-if="form.errors.message" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.message }}</div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-4 px-6 rounded-full bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-500 dark:hover:bg-cyan-400 text-white font-bold text-lg shadow-lg hover:shadow-xl transform transition-all duration-300 hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-cyan-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="form.processing">Sending...</span>
                        <span v-else>Send Message</span>
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
