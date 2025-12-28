import { defineStore } from 'pinia';

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [],
        currency: 'MXN', // 'MXN' or 'USD'
        isCartOpen: false,
    }),
    getters: {
        cartCount: (state) => state.items.length,
        cartTotal: (state) => {
            return state.items.reduce((total, item) => {
                const price = state.currency === 'MXN' ? item.price_mxn : item.price_usd;
                return total + price;
            }, 0);
        },
        // Mock exchange rate: 1 USD = 20 MXN
        exchangeRate: () => 20,
    },
    actions: {
        addToCart(service) {
            // Check if item already exists (optional, maybe allow duplicates or just quantity)
            // For now, simple list
            this.items.push(service);
        },
        removeFromCart(index) {
            this.items.splice(index, 1);
        },
        toggleCurrency() {
            this.currency = this.currency === 'MXN' ? 'USD' : 'MXN';
        },
        clearCart() {
            this.items = [];
        }
    },
    persist: true,
});
