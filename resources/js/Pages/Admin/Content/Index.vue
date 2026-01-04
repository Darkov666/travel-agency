<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    blocks: Object, // Grouped by 'group'
});

const groups = ref(['home', 'general', 'footer']); // Known groups
// Helper to flatten blocks for the form
const getBlocks = () => {
    let all = [];
    // Initialize defaults if missing
    const defaults = [
        { 
            key: 'home_hero_title', 
            group: 'home', 
            type: 'html', 
            label: 'Home Hero Title',
            value: 'Experience <span class="text-secondary-300">Paradise</span> <br class="hidden md:block" /> with Comfort'
        },
        { 
            key: 'home_hero_subtitle', 
            group: 'home', 
            type: 'text', 
            label: 'Home Hero Subtitle',
            value: 'Premium airport transfers and exclusive tours in Cancun & Riviera Maya.'
        },
        { 
            key: 'home_cta_text', 
            group: 'home', 
            type: 'text', 
            label: 'Home Call-to-Action Text',
            value: 'Explore Services'
        },
        // Value Propositions (JSON for list)
        {
            key: 'home_value_props',
            group: 'home',
            type: 'json',
            label: 'Home Value Propositions (JSON)',
            value: JSON.stringify([
                { 
                    title: 'Punctuality Guaranteed', 
                    description: 'We track your flight to ensure we are there when you land. No waiting.',
                    icon: 'clock' 
                },
                { 
                    title: 'Safe & Secure', 
                    description: 'Certified drivers and sanitized luxury vehicles for your peace of mind.',
                    icon: 'shield' 
                },
                { 
                    title: 'Best Price', 
                    description: 'Competitive rates with no hidden fees. Luxury service at fair prices.',
                    icon: 'dollar' 
                }
            ], null, 2)
        },
        // Explore Section
        {
            key: 'home_explore_title',
            group: 'home',
            type: 'text',
            label: 'Explore Section Title',
            value: 'Explore the Ancient World'
        },
        {
            key: 'home_explore_text',
            group: 'home',
            type: 'text',
            label: 'Explore Section Text',
            value: 'Discover the magic of Chichen Itza, Tulum, and Coba with our private tours. We take you there in comfort and style, allowing you to explore at your own pace.'
        },
        // Fleet Section
        {
            key: 'home_fleet_title',
            group: 'home',
            type: 'text',
            label: 'Fleet Section Title',
            value: 'Travel in Luxury'
        },
        {
            key: 'home_fleet_text',
            group: 'home',
            type: 'text',
            label: 'Fleet Section Text',
            value: 'Our fleet consists of modern, air-conditioned vehicles including private vans and luxury SUVs. Enjoy complimentary water and a smooth ride to your destination.'
        },
        // Fleet Features (JSON for list)
        {
            key: 'home_fleet_features',
            group: 'home',
            type: 'json',
            label: 'Fleet Features List (JSON)',
            value: JSON.stringify([
                "Private Air-Conditioned Vans",
                "Professional Bilingual Drivers",
                "Flight Monitoring included"
            ], null, 2)
        },
        // Images
        {
            key: 'home_hero_image',
            group: 'home',
            type: 'image',
            label: 'Home Hero Image',
            value: '/images/hero.jpg'
        },
        {
            key: 'home_explore_image',
            group: 'home',
            type: 'image',
            label: 'Explore Section Image',
            value: 'https://placehold.co/800x600/FF7F50/FFFFFF/png?text=Mayan+Ruins'
        },
        {
            key: 'home_fleet_image',
            group: 'home',
            type: 'image',
            label: 'Fleet Section Image',
            value: 'https://placehold.co/800x600/F4A460/FFFFFF/png?text=Luxury+Van'
        }
    ];
    
    // Merge defaults
    defaults.forEach(def => {
        all.push({
            key: def.key,
            value: def.value, // Default value if not overwritten
            group: def.group,
            type: def.type,
            file: null // Placeholder for upload
        });
    });

    // Overwrite with server data
    Object.keys(props.blocks).forEach(grp => {
        props.blocks[grp].forEach(blk => {
            const idx = all.findIndex(a => a.key === blk.key);
            if (idx >= 0) {
                all[idx].value = blk.value;
                all[idx].file = null; // Ensure file prop exists
            } else {
                all.push({ ...blk, file: null });
            }
        });
    });

    return all;
};

const form = useForm({
    blocks: getBlocks(),
});

const submit = () => {
    form.post(route('admin.content.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Updated
        }
    });
};

const addBlock = () => {
    form.blocks.push({ key: '', value: '', group: 'general', type: 'text', file: null });
};
</script>

<template>
    <Head title="Content & Settings" />
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Site Content
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="mb-4 flex justify-between">
                            <p class="text-sm text-gray-500">Manage dynamic text and settings for your public pages.</p>
                            <button type="button" @click="addBlock" class="text-sm text-cyan-600 hover:text-cyan-800">+ Add Custom Block</button>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div v-for="(block, index) in form.blocks" :key="index" class="p-4 border border-gray-100 rounded bg-gray-50 flex flex-col md:flex-row gap-4 items-start">
                                <div class="w-full md:w-1/4">
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Key</label>
                                    <input v-model="block.key" type="text" class="w-full text-sm rounded border-gray-300 bg-white" placeholder="unique_key_name">
                                    <select v-model="block.group" class="w-full text-xs mt-2 rounded border-gray-300 bg-white text-gray-500">
                                        <option value="home">Home Page</option>
                                        <option value="general">General</option>
                                        <option value="footer">Footer</option>
                                    </select>
                                </div>
                                <div class="w-full md:w-3/4">
                                     <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Content</label>
                                     <textarea v-if="block.type === 'text' || block.type === 'html' || block.type === 'json'" v-model="block.value" rows="3" class="w-full rounded border-gray-300 font-mono text-sm"></textarea>
                                     <div v-else-if="block.type === 'image'">
                                         <div v-if="block.value" class="mb-2">
                                             <img :src="block.value" class="h-20 w-auto object-cover rounded border border-gray-300">
                                         </div>
                                         <input type="file" @input="block.file = $event.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" />
                                         <input type="hidden" v-model="block.value">
                                     </div>
                                     <input v-else v-model="block.value" type="text" class="w-full rounded border-gray-300">
                                </div>
                                <div class="w-10 pt-6">
                                    <button type="button" @click="form.blocks.splice(index, 1)" class="text-red-400 hover:text-red-600" title="Remove">
                                        &times;
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" :disabled="form.processing" class="bg-cyan-600 text-white px-6 py-2 rounded shadow hover:bg-cyan-700">
                                Save All Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
