<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    blocks: Object, // Grouped by 'group'
});

import { computed } from 'vue';

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
        // Value Props (Cards)
        { key: 'home_card1_title', group: 'home', type: 'text', label: 'Card 1 Title', value: 'Private Transfers' },
        { key: 'home_card1_text', group: 'home', type: 'text', label: 'Card 1 Text', value: 'Reliable airport pickup and drop-off in luxury vehicles.' },
        { key: 'home_card1_icon', group: 'home', type: 'text', label: 'Card 1 Icon (Emoji)', value: '✈️' },
        { key: 'home_card1_image', group: 'home', type: 'image', label: 'Card 1 Custom Image (Optional)', value: '' },

        { key: 'home_card2_title', group: 'home', type: 'text', label: 'Card 2 Title', value: 'Exclusive Tours' },
        { key: 'home_card2_text', group: 'home', type: 'text', label: 'Card 2 Text', value: 'Discover hidden gems and ancient ruins with expert guides.' },
        { key: 'home_card2_icon', group: 'home', type: 'text', label: 'Card 2 Icon (Emoji)', value: '🗿' },
        { key: 'home_card2_image', group: 'home', type: 'image', label: 'Card 2 Custom Image (Optional)', value: '' },

        { key: 'home_card3_title', group: 'home', type: 'text', label: 'Card 3 Title', value: 'VIP Service' },
        { key: 'home_card3_text', group: 'home', type: 'text', label: 'Card 3 Text', value: 'Personalized attention and 24/7 support for your trip.' },
        { key: 'home_card3_icon', group: 'home', type: 'text', label: 'Card 3 Icon (Emoji)', value: '💎' },
        { key: 'home_card3_image', group: 'home', type: 'image', label: 'Card 3 Custom Image (Optional)', value: '' },

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
        },
        // Latest Packages Section
        {
            key: 'home_featured_title',
            group: 'home',
            type: 'text',
            label: 'Packages Section Title',
            value: 'Latest Packages'
        },
        {
            key: 'home_featured_subtitle',
            group: 'home',
            type: 'text',
            label: 'Packages Section Subtitle',
            value: 'Check out our most recent additions.'
        },
        {
            key: 'home_featured_align',
            group: 'home',
            type: 'select',
            label: 'Packages Title Alignment',
            value: 'text-center',
            options: [
                { value: 'text-left', label: 'Left' },
                { value: 'text-center', label: 'Center' },
                { value: 'text-right', label: 'Right' }
            ]
        },
        {
            key: 'home_featured_font_family',
            group: 'home',
            type: 'select',
            label: 'Packages Font Family',
            value: 'font-serif',
            options: [
                { value: 'font-sans', label: 'Sans Serif' },
                { value: 'font-serif', label: 'Serif' },
                { value: 'font-mono', label: 'Monospace' }
            ]
        },
        {
            key: 'home_featured_font_size',
            group: 'home',
            type: 'select',
            label: 'Packages Title Size',
            value: 'text-3xl md:text-4xl',
            options: [
                { value: 'text-2xl md:text-3xl', label: 'Small' },
                { value: 'text-3xl md:text-4xl', label: 'Medium' },
                { value: 'text-4xl md:text-5xl', label: 'Large' },
                { value: 'text-5xl md:text-6xl', label: 'Extra Large' }
            ]
        },
        {
            key: 'home_featured_font_size_sub',
            group: 'home',
            type: 'select',
            label: 'Packages Subtitle Size',
            value: 'text-lg',
            options: [
                { value: 'text-base', label: 'Small' },
                { value: 'text-lg', label: 'Medium' },
                { value: 'text-xl', label: 'Large' }
            ]
        },
        // Hero Styling
        { key: 'home_hero_align', group: 'home', type: 'select', label: 'Hero Text Alignment', value: 'text-center', options: [{ value: 'text-left', label: 'Left' }, { value: 'text-center', label: 'Center' }, { value: 'text-right', label: 'Right' }] },
        { key: 'home_hero_font_family', group: 'home', type: 'select', label: 'Hero Font Family', value: 'font-serif', options: [{ value: 'font-sans', label: 'Sans Serif' }, { value: 'font-serif', label: 'Serif' }, { value: 'font-mono', label: 'Monospace' }] },
        { key: 'home_hero_title_size', group: 'home', type: 'select', label: 'Hero Title Size', value: 'text-4xl sm:text-5xl md:text-7xl', options: [{ value: 'text-3xl sm:text-4xl md:text-6xl', label: 'Small' }, { value: 'text-4xl sm:text-5xl md:text-7xl', label: 'Medium' }, { value: 'text-5xl sm:text-6xl md:text-8xl', label: 'Large' }] },
        { key: 'home_hero_subtitle_size', group: 'home', type: 'select', label: 'Hero Subtitle Size', value: 'text-xl sm:text-2xl', options: [{ value: 'text-lg sm:text-xl', label: 'Small' }, { value: 'text-xl sm:text-2xl', label: 'Medium' }, { value: 'text-2xl sm:text-3xl', label: 'Large' }] },

        // Value Props (Cards) Styling
        { key: 'home_cards_align', group: 'home', type: 'select', label: 'Cards Text Alignment', value: 'text-center', options: [{ value: 'text-left', label: 'Left' }, { value: 'text-center', label: 'Center' }, { value: 'text-right', label: 'Right' }] },
        { key: 'home_cards_font_family', group: 'home', type: 'select', label: 'Cards Font Family', value: 'font-sans', options: [{ value: 'font-sans', label: 'Sans Serif' }, { value: 'font-serif', label: 'Serif' }, { value: 'font-mono', label: 'Monospace' }] },
        { key: 'home_cards_title_size', group: 'home', type: 'select', label: 'Cards Title Size', value: 'text-xl', options: [{ value: 'text-lg', label: 'Small' }, { value: 'text-xl', label: 'Medium' }, { value: 'text-2xl', label: 'Large' }] },
        { key: 'home_cards_text_size', group: 'home', type: 'select', label: 'Cards Text Size', value: 'text-base', options: [{ value: 'text-sm', label: 'Small' }, { value: 'text-base', label: 'Medium' }, { value: 'text-lg', label: 'Large' }] },

        // Explore Styling
        { key: 'home_explore_align', group: 'home', type: 'select', label: 'Explore Text Alignment', value: 'text-left', options: [{ value: 'text-left', label: 'Left' }, { value: 'text-center', label: 'Center' }, { value: 'text-right', label: 'Right' }] },
        { key: 'home_explore_font_family', group: 'home', type: 'select', label: 'Explore Font Family', value: 'font-serif', options: [{ value: 'font-sans', label: 'Sans Serif' }, { value: 'font-serif', label: 'Serif' }, { value: 'font-mono', label: 'Monospace' }] },
        { key: 'home_explore_title_size', group: 'home', type: 'select', label: 'Explore Title Size', value: 'text-4xl', options: [{ value: 'text-3xl', label: 'Small' }, { value: 'text-4xl', label: 'Medium' }, { value: 'text-5xl', label: 'Large' }] },
        { key: 'home_explore_text_size', group: 'home', type: 'select', label: 'Explore Text Size', value: 'text-lg', options: [{ value: 'text-base', label: 'Small' }, { value: 'text-lg', label: 'Medium' }, { value: 'text-xl', label: 'Large' }] },

        // Fleet Styling
        { key: 'home_fleet_align', group: 'home', type: 'select', label: 'Fleet Text Alignment', value: 'text-left', options: [{ value: 'text-left', label: 'Left' }, { value: 'text-center', label: 'Center' }, { value: 'text-right', label: 'Right' }] },
        { key: 'home_fleet_font_family', group: 'home', type: 'select', label: 'Fleet Font Family', value: 'font-serif', options: [{ value: 'font-sans', label: 'Sans Serif' }, { value: 'font-serif', label: 'Serif' }, { value: 'font-mono', label: 'Monospace' }] },
        { key: 'home_fleet_title_size', group: 'home', type: 'select', label: 'Fleet Title Size', value: 'text-4xl', options: [{ value: 'text-3xl', label: 'Small' }, { value: 'text-4xl', label: 'Medium' }, { value: 'text-5xl', label: 'Large' }] },
        { key: 'home_fleet_text_size', group: 'home', type: 'select', label: 'Fleet Text Size', value: 'text-lg', options: [{ value: 'text-base', label: 'Small' }, { value: 'text-lg', label: 'Medium' }, { value: 'text-xl', label: 'Large' }] },
    ];
    
    // Merge defaults
    defaults.forEach(def => {
        all.push({
            key: def.key,
            value: def.value, // Default value if not overwritten
            group: def.group,
            type: def.type,
            options: def.options, // Pass options for selects
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

// Keys explicitly rendered in custom slots, to exclude from generic loop
const renderedKeys = [
    'home_hero_title', 'home_hero_subtitle', 'home_hero_image', 'home_cta_text',
    'home_hero_align', 'home_hero_font_family', 'home_hero_title_size', 'home_hero_subtitle_size',
    
    'home_card1_title', 'home_card1_text', 'home_card1_icon', 'home_card1_image',
    'home_card2_title', 'home_card2_text', 'home_card2_icon', 'home_card2_image',
    'home_card3_title', 'home_card3_text', 'home_card3_icon', 'home_card3_image',
    'home_cards_align', 'home_cards_font_family', 'home_cards_title_size', 'home_cards_text_size',
    
    'home_explore_title', 'home_explore_text', 'home_explore_image',
    'home_explore_align', 'home_explore_font_family', 'home_explore_title_size', 'home_explore_text_size',

    'home_fleet_title', 'home_fleet_text', 'home_fleet_image', 'home_fleet_features',
    'home_fleet_align', 'home_fleet_font_family', 'home_fleet_title_size', 'home_fleet_text_size',

    'home_featured_title', 'home_featured_subtitle', 'home_featured_align', 'home_featured_font_family', 'home_featured_font_size', 'home_featured_font_size_sub'
];

const remainingBlocks = computed(() => {
    return form.blocks.filter(b => !renderedKeys.includes(b.key));
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
                        
                        <!-- Hero Section -->
                        <div class="mb-8 border border-gray-200 p-4 rounded-lg bg-white">
                            <h3 class="font-bold text-lg mb-4 bg-gray-50 p-2 border-b">Hero Section</h3>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template v-for="key in ['home_hero_title', 'home_hero_subtitle', 'home_cta_text']" :key="key">
                                    <div v-if="form.blocks.find(b => b.key === key)" class="col-span-2 md:col-span-1">
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">{{ form.blocks.find(b => b.key === key).label }}</label>
                                        <textarea v-if="form.blocks.find(b => b.key === key).type === 'html' || form.blocks.find(b => b.key === key).type === 'text'" v-model="form.blocks.find(b => b.key === key).value" rows="2" class="w-full text-sm rounded border-gray-300"></textarea>
                                        <input v-else v-model="form.blocks.find(b => b.key === key).value" type="text" class="w-full text-sm rounded border-gray-300">
                                    </div>
                                </template>
                                    <div v-if="form.blocks.find(b => b.key === 'home_hero_image')" class="col-span-2">
                                         <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Hero Image</label>
                                         <div v-if="form.blocks.find(b => b.key === 'home_hero_image').value" class="mb-2">
                                             <img :src="form.blocks.find(b => b.key === 'home_hero_image').value" class="h-20 w-auto object-cover rounded">
                                         </div>
                                         <input type="file" @input="form.blocks.find(b => b.key === 'home_hero_image').file = $event.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" />
                                    </div>
                                    <!-- Styling Controls -->
                                    <div class="col-span-2 border-t pt-4 mt-2">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Styling</h4>
                                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                                             <template v-for="key in ['home_hero_align', 'home_hero_font_family', 'home_hero_title_size', 'home_hero_subtitle_size']" :key="key">
                                                <div v-if="form.blocks.find(b => b.key === key)">
                                                     <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">{{ form.blocks.find(b => b.key === key).label }}</label>
                                                    <select v-model="form.blocks.find(b => b.key === key).value" class="w-full text-xs rounded border-gray-200 py-1">
                                                        <option v-for="opt in (form.blocks.find(b => b.key === key).options || [])" :key="opt.value" :value="opt.value">
                                                            {{ opt.label }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                             </div>
                        </div>

                        <!-- Value Props Section -->
                        <div class="mb-8 border border-gray-200 p-4 rounded-lg bg-white">
                            <h3 class="font-bold text-lg mb-4 bg-gray-50 p-2 border-b flex justify-between items-center">
                                Value Props Cards (Hero Bottom)
                                <div class="flex gap-2">
                                     <template v-for="key in ['home_cards_align', 'home_cards_font_family', 'home_cards_title_size', 'home_cards_text_size']" :key="key">
                                        <div v-if="form.blocks.find(b => b.key === key)" class="w-24">
                                            <select v-model="form.blocks.find(b => b.key === key).value" class="w-full text-xs rounded border-gray-200 py-1">
                                                <option v-for="opt in (form.blocks.find(b => b.key === key).options || [])" :key="opt.value" :value="opt.value">
                                                    {{ opt.label }}
                                                </option>
                                            </select>
                                        </div>
                                    </template>
                                </div>
                            </h3>
                            
                            <!-- Card 1 -->
                            <div class="mb-6 p-4 border border-gray-100 rounded bg-gray-50">
                                <h4 class="font-semibold text-gray-700 mb-2">Card 1</h4>
                                <div v-if="form.blocks.find(b => b.key === 'home_card1_title')" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Title</label>
                                        <input v-model="form.blocks.find(b => b.key === 'home_card1_title').value" type="text" class="w-full text-sm rounded border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Icon (Emoji)</label>
                                        <input v-model="form.blocks.find(b => b.key === 'home_card1_icon').value" type="text" class="w-full text-sm rounded border-gray-300">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Text</label>
                                        <textarea v-model="form.blocks.find(b => b.key === 'home_card1_text').value" rows="2" class="w-full text-sm rounded border-gray-300"></textarea>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Custom Icon Image (Optional)</label>
                                        <div v-if="form.blocks.find(b => b.key === 'home_card1_image').value" class="mb-2">
                                            <img :src="form.blocks.find(b => b.key === 'home_card1_image').value" class="h-10 w-auto object-cover">
                                        </div>
                                        <input type="file" @input="form.blocks.find(b => b.key === 'home_card1_image').file = $event.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" />
                                    </div>
                                </div>
                            </div>

                             <!-- Card 2 -->
                            <div class="mb-6 p-4 border border-gray-100 rounded bg-gray-50">
                                <h4 class="font-semibold text-gray-700 mb-2">Card 2</h4>
                                <div v-if="form.blocks.find(b => b.key === 'home_card2_title')" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Title</label>
                                        <input v-model="form.blocks.find(b => b.key === 'home_card2_title').value" type="text" class="w-full text-sm rounded border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Icon (Emoji)</label>
                                        <input v-model="form.blocks.find(b => b.key === 'home_card2_icon').value" type="text" class="w-full text-sm rounded border-gray-300">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Text</label>
                                        <textarea v-model="form.blocks.find(b => b.key === 'home_card2_text').value" rows="2" class="w-full text-sm rounded border-gray-300"></textarea>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Custom Icon Image (Optional)</label>
                                        <div v-if="form.blocks.find(b => b.key === 'home_card2_image').value" class="mb-2">
                                            <img :src="form.blocks.find(b => b.key === 'home_card2_image').value" class="h-10 w-auto object-cover">
                                        </div>
                                        <input type="file" @input="form.blocks.find(b => b.key === 'home_card2_image').file = $event.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" />
                                    </div>
                                </div>
                            </div>
                            
                             <!-- Card 3 -->
                            <div class="mb-6 p-4 border border-gray-100 rounded bg-gray-50">
                                <h4 class="font-semibold text-gray-700 mb-2">Card 3</h4>
                                <div v-if="form.blocks.find(b => b.key === 'home_card3_title')" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Title</label>
                                        <input v-model="form.blocks.find(b => b.key === 'home_card3_title').value" type="text" class="w-full text-sm rounded border-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Icon (Emoji)</label>
                                        <input v-model="form.blocks.find(b => b.key === 'home_card3_icon').value" type="text" class="w-full text-sm rounded border-gray-300">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Text</label>
                                        <textarea v-model="form.blocks.find(b => b.key === 'home_card3_text').value" rows="2" class="w-full text-sm rounded border-gray-300"></textarea>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Custom Icon Image (Optional)</label>
                                        <div v-if="form.blocks.find(b => b.key === 'home_card3_image').value" class="mb-2">
                                            <img :src="form.blocks.find(b => b.key === 'home_card3_image').value" class="h-10 w-auto object-cover">
                                        </div>
                                        <input type="file" @input="form.blocks.find(b => b.key === 'home_card3_image').file = $event.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Explore Section -->
                        <div class="mb-8 border border-gray-200 p-4 rounded-lg bg-white">
                            <h3 class="font-bold text-lg mb-4 bg-gray-50 p-2 border-b">Explore Section</h3>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template v-for="key in ['home_explore_title', 'home_explore_text']" :key="key">
                                    <div v-if="form.blocks.find(b => b.key === key)" class="col-span-2 md:col-span-1">
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">{{ form.blocks.find(b => b.key === key).label }}</label>
                                        <textarea v-model="form.blocks.find(b => b.key === key).value" rows="2" class="w-full text-sm rounded border-gray-300"></textarea>
                                    </div>
                                </template>
                                <div v-if="form.blocks.find(b => b.key === 'home_explore_image')" class="col-span-2">
                                     <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Explore Image</label>
                                     <div v-if="form.blocks.find(b => b.key === 'home_explore_image').value" class="mb-2">
                                         <img :src="form.blocks.find(b => b.key === 'home_explore_image').value" class="h-20 w-auto object-cover rounded">
                                     </div>
                                         <input type="file" @input="form.blocks.find(b => b.key === 'home_explore_image').file = $event.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" />
                                </div>
                                 <!-- Styling Controls -->
                                <div class="col-span-2 border-t pt-4 mt-2">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Styling</h4>
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                                         <template v-for="key in ['home_explore_align', 'home_explore_font_family', 'home_explore_title_size', 'home_explore_text_size']" :key="key">
                                            <div v-if="form.blocks.find(b => b.key === key)">
                                                 <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">{{ form.blocks.find(b => b.key === key).label }}</label>
                                                <select v-model="form.blocks.find(b => b.key === key).value" class="w-full text-xs rounded border-gray-200 py-1">
                                                    <option v-for="opt in (form.blocks.find(b => b.key === key).options || [])" :key="opt.value" :value="opt.value">
                                                        {{ opt.label }}
                                                    </option>
                                                </select>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                             </div>
                        </div>

                        <!-- Fleet Section -->
                        <div class="mb-8 border border-gray-200 p-4 rounded-lg bg-white">
                            <h3 class="font-bold text-lg mb-4 bg-gray-50 p-2 border-b">Fleet Section</h3>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template v-for="key in ['home_fleet_title', 'home_fleet_text']" :key="key">
                                    <div v-if="form.blocks.find(b => b.key === key)" class="col-span-2 md:col-span-1">
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">{{ form.blocks.find(b => b.key === key).label }}</label>
                                        <textarea v-model="form.blocks.find(b => b.key === key).value" rows="2" class="w-full text-sm rounded border-gray-300"></textarea>
                                    </div>
                                </template>
                                <div v-if="form.blocks.find(b => b.key === 'home_fleet_image')" class="col-span-2">
                                     <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fleet Image</label>
                                     <div v-if="form.blocks.find(b => b.key === 'home_fleet_image').value" class="mb-2">
                                         <img :src="form.blocks.find(b => b.key === 'home_fleet_image').value" class="h-20 w-auto object-cover rounded">
                                     </div>
                                     <input type="file" @input="form.blocks.find(b => b.key === 'home_fleet_image').file = $event.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100" />
                                </div>
                                <div v-if="form.blocks.find(b => b.key === 'home_fleet_features')" class="col-span-2">
                                     <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fleet Features (JSON List)</label>
                                     <textarea v-model="form.blocks.find(b => b.key === 'home_fleet_features').value" rows="3" class="w-full text-sm rounded border-gray-300 font-mono"></textarea>
                                </div>
                                <!-- Styling Controls -->
                                <div class="col-span-2 border-t pt-4 mt-2">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Styling</h4>
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                                         <template v-for="key in ['home_fleet_align', 'home_fleet_font_family', 'home_fleet_title_size', 'home_fleet_text_size']" :key="key">
                                            <div v-if="form.blocks.find(b => b.key === key)">
                                                 <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">{{ form.blocks.find(b => b.key === key).label }}</label>
                                                <select v-model="form.blocks.find(b => b.key === key).value" class="w-full text-xs rounded border-gray-200 py-1">
                                                    <option v-for="opt in (form.blocks.find(b => b.key === key).options || [])" :key="opt.value" :value="opt.value">
                                                        {{ opt.label }}
                                                    </option>
                                                </select>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                             </div>
                        </div>

                        <!-- Latest Packages Configuration -->
                         <div class="mb-8 border border-gray-200 p-4 rounded-lg bg-white">
                            <h3 class="font-bold text-lg mb-4 bg-gray-50 p-2 border-b">Latest Packages Configuration</h3>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Text Fields -->
                                <template v-for="key in ['home_featured_title', 'home_featured_subtitle']" :key="key">
                                    <div v-if="form.blocks.find(b => b.key === key)" class="col-span-2 md:col-span-1">
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">{{ form.blocks.find(b => b.key === key).label }}</label>
                                        <textarea v-model="form.blocks.find(b => b.key === key).value" rows="1" class="w-full text-sm rounded border-gray-300"></textarea>
                                    </div>
                                </template>
                                <!-- Select Fields -->
                                <template v-for="key in ['home_featured_align', 'home_featured_font_family', 'home_featured_font_size', 'home_featured_font_size_sub']" :key="key">
                                    <div v-if="form.blocks.find(b => b.key === key)" class="col-span-2 md:col-span-1">
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">{{ form.blocks.find(b => b.key === key).label }}</label>
                                        <select v-model="form.blocks.find(b => b.key === key).value" class="w-full text-sm rounded border-gray-300">
                                            <option v-for="opt in (form.blocks.find(b => b.key === key).options || [])" :key="opt.value" :value="opt.value">
                                                {{ opt.label }}
                                            </option>
                                        </select>
                                    </div>
                                </template>
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
