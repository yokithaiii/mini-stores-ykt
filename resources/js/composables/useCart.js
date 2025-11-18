import { ref, computed } from 'vue';

// Глобальное состояние корзины
const cart = ref([]);
const isLoaded = ref(false);

export function useCart() {
    const loadCart = () => {
        if (isLoaded.value) return; // Загружаем только один раз
        
        const saved = localStorage.getItem('shop_cart');
        if (saved) {
            try {
                cart.value = JSON.parse(saved);
            } catch (error) {
                console.error('Failed to load cart', error);
            }
        }
        isLoaded.value = true;
    };

    const saveCart = () => {
        localStorage.setItem('shop_cart', JSON.stringify(cart.value));
    };

    const cartItemsCount = computed(() => {
        return cart.value.reduce((sum, item) => sum + item.cartQuantity, 0);
    });

    const addToCart = (item) => {
        const existingIndex = cart.value.findIndex(i => 
            i.id === item.id && i.selectedSize === item.selectedSize
        );
        
        if (existingIndex >= 0) {
            cart.value[existingIndex].cartQuantity++;
        } else {
            cart.value.push(item);
        }
        
        saveCart();
    };

    const removeFromCart = (index) => {
        cart.value.splice(index, 1);
        saveCart();
    };

    const updateQuantity = (index, quantity) => {
        if (cart.value[index]) {
            cart.value[index].cartQuantity = quantity;
            saveCart();
        }
    };

    const clearCart = () => {
        cart.value = [];
        saveCart();
    };

    return {
        cart,
        cartItemsCount,
        loadCart,
        saveCart,
        addToCart,
        removeFromCart,
        updateQuantity,
        clearCart,
    };
}
