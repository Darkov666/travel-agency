<script setup>
import { useCartStore } from '@/Stores/cart';

const cartStore = useCartStore();
</script>

<template>
    <button 
        @click="cartStore.toggleCurrency()" 
        class="flex items-center space-x-1 px-3 py-1 rounded-full border border-secondary-300 dark:border-gray-400 hover:bg-secondary-100 dark:hover:bg-gray-700 transition text-sm font-medium text-secondary-700 dark:text-gray-200"
    >
        <span :class="{'text-primary-600 dark:text-cyan-400 font-bold': cartStore.currency === 'MXN'}">MXN</span>
        <span class="text-secondary-400 dark:text-gray-500">/</span>
        <span :class="{'text-primary-600 dark:text-cyan-400 font-bold': cartStore.currency === 'USD'}">USD</span>
    </button>
</template>
