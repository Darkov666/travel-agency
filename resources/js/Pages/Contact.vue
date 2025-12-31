<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ContactSection from '@/Components/ContactSection.vue';

</script>

<template>
    <Head title="Contact Us" />

    <MainLayout>
        <div class="pt-20">
             <ContactSection />
        </div>
    </MainLayout>
</template>
