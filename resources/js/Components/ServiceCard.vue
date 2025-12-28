<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/cart';
import { wTrans } from 'laravel-vue-i18n';
import axios from 'axios';

const props = defineProps({
    service: {
        type: Object,
        required: true
    },
    isProduct: {
        type: Boolean,
        default: false
    },
    isSpecial: {
        type: Boolean,
        default: false
    }
});

const t = wTrans;
const cartStore = useCartStore();

const price = computed(() => {
    return cartStore.currency === 'MXN' ? props.service.price_mxn : props.service.price_usd;
});

const currencySymbol = computed(() => {
    return cartStore.currency === 'MXN' ? 'MX$' : 'US$';
});

const addToCart = async () => {
    console.log('Adding to cart (axios):', props.service.id);
    try {
        const response = await axios.post('/cart/add', {
            service_id: props.service.id,
            quantity: 1
        });
        console.log('Added to cart successfully', response.data);
        // Reload page to update cart count (or use store if we had one synced)
        window.location.reload(); 
    } catch (error) {
        console.error('Error adding to cart:', error);
        alert('Error al agregar al carrito: ' + (error.response?.data?.message || error.message));
    }
};

const bookNow = () => {
    const title = props.service.title.toLowerCase();
    const type = props.service.type;
    let route = '/booking/individual';
    let query = { service_id: props.service.id };

    if (['workshop', 'conference', 'talk', 'training'].includes(type)) {
        route = '/booking/workshop';
        // query is already set
    } else if (title.includes('pareja') || title.includes('couple')) {
        route = '/booking/group';
        query.type = 'couples';
    } else if (title.includes('grupo') || title.includes('group') || title.includes('familia') || title.includes('family')) {
        route = '/booking/group';
        query.type = 'group';
    }

    router.visit(route, { data: query });
};
const bookSpecial = () => {
    router.visit('/booking/special', { data: { service_id: props.service.id } });
};
</script>

<template>
    <div class="bg-primary-50 dark:bg-secondary-800 rounded-xl overflow-hidden hover:shadow-lg transition duration-300 border border-primary-100 dark:border-secondary-700 flex flex-col h-full">
        <!-- Image or Video -->
        <div class="h-48 w-full overflow-hidden relative group">
            <img 
                v-if="!service.video_url && service.image" 
                :src="service.image" 
                :alt="service.title" 
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
            />
            <div v-else-if="service.video_url" class="w-full h-full bg-black flex items-center justify-center">
                 <img 
                    :src="service.video_url" 
                    :alt="service.title" 
                    class="w-full h-full object-cover"
                />
                <!-- Play Icon Overlay -->
                <div class="absolute inset-0 flex items-center justify-center bg-black/30 group-hover:bg-black/20 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white opacity-80 group-hover:opacity-100 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div v-else class="w-full h-full bg-secondary-200 dark:bg-secondary-700 flex items-center justify-center">
                <svg class="h-12 w-12 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <div class="p-8 flex flex-col flex-grow">
            <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-3">{{ service.title }}</h3>
            <p class="text-secondary-600 dark:text-secondary-300 mb-6 line-clamp-3 flex-grow">{{ service.description }}</p>
        
            <div class="mt-auto space-y-4">
                <div class="mt-4 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <span v-if="!isSpecial && !['workshop', 'conference', 'talk', 'training'].includes(service.type)" class="text-2xl font-bold text-primary-600">
                            {{ currencySymbol }}{{ price }}
                            <span v-if="!isProduct" class="text-sm text-secondary-500 font-normal">/ {{ service.duration_minutes }}m</span>
                        </span>
                        <span v-else-if="isSpecial" class="text-xl font-bold text-primary-600">
                            Cotizar
                        </span>
                        <!-- Workshop: No price shown -->
                    </div>
                    
                    <div class="flex gap-2">
                        <button 
                            v-if="!isSpecial && !isProduct && !['workshop', 'conference', 'talk', 'training'].includes(service.type)"
                            @click="addToCart"
                            class="flex-1 px-3 py-2 border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50 transition font-medium text-sm text-center"
                        >
                            {{ t('services.add_to_cart') }}
                        </button>

                        <button 
                            @click="isSpecial ? bookSpecial() : bookNow()"
                            class="flex-1 px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition font-medium text-sm shadow-sm text-center"
                        >
                            {{ 
                                isSpecial ? 'Agendar Consulta' : 
                                (isProduct ? 'Comprar Ahora' : 
                                (['workshop', 'conference', 'talk', 'training'].includes(service.type) ? 'Reservar una reunión' : t('services.book_now'))) 
                            }}
                        </button>
                    </div>
                </div>
                
                <div class="text-center pt-2">
                    <Link :href="`/services`" class="text-secondary-500 hover:text-primary-600 dark:text-secondary-400 dark:hover:text-primary-300 text-sm underline">
                        {{ t('services.learn_more') }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
