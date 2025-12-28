<script setup>
import { useCartStore } from '@/Stores/cart';

const cartStore = useCartStore();
</script>

<template>
    <button 
        @click="cartStore.toggleCurrency()" 
        class="flex items-center space-x-1 px-3 py-1 rounded-full border border-secondary-300 dark:border-secondary-600 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition text-sm font-medium text-secondary-700 dark:text-secondary-300"
    >
        <span :class="{'text-primary-600 font-bold': cartStore.currency === 'MXN'}">MXN</span>
        <span class="text-secondary-400">/</span>
        <span :class="{'text-primary-600 font-bold': cartStore.currency === 'USD'}">USD</span>
    </button>
</template>
