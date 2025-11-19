<template>
    <transition name="cart">
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-start justify-end bg-black/40 backdrop-blur-sm"
            @click.self="$emit('close')"
        >
            <div class="h-full w-full max-w-md overflow-y-auto bg-white shadow-2xl">
                <div class="sticky top-0 z-10 border-b border-gray-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900">Корзина</h2>
                        <button
                            class="text-gray-600 hover:text-gray-900"
                            @click="$emit('close')"
                        >
                            Закрыть
                        </button>
                    </div>
                </div>

                <div v-if="!cart.length" class="p-6 text-center">
                    <p class="text-gray-500">Корзина пуста</p>
                </div>

                <div v-else class="p-6">
                    <ul class="space-y-4">
                        <li
                            v-for="(item, index) in cart"
                            :key="`${item.id}-${item.selectedSize || 'default'}-${index}`"
                            class="flex gap-4 rounded-xl bg-gray-50 p-4"
                        >
                            <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200">
                                <img 
                                    v-if="item.image" 
                                    :src="item.image" 
                                    :alt="item.name"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900">{{ item.name }}</h3>
                                <p v-if="item.selectedSize" class="text-xs text-gray-500">Размер: {{ item.selectedSize }}</p>
                                <div class="flex items-center gap-2">
                                    <p 
                                        v-if="item.discount_type !== 'none' && item.discount_value"
                                        class="text-xs text-gray-400 line-through"
                                    >
                                        {{ formatPrice(item.price) }}
                                    </p>
                                    <p class="text-sm font-semibold text-gray-900">{{ formatPrice(getDiscountedPrice(item)) }}</p>
                                </div>
                                
                                <div class="mt-2 flex items-center gap-2">
                                    <button
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100"
                                        @click="decreaseQuantity(index)"
                                    >
                                        −
                                    </button>
                                    <span class="w-8 text-center font-semibold">{{ item.cartQuantity }}</span>
                                    <button
                                        class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
                                        :disabled="item.cartQuantity >= getMaxQuantityForItem(item)"
                                        @click="increaseQuantity(index)"
                                    >
                                        +
                                    </button>
                                    <button
                                        class="ml-auto text-sm font-semibold text-red-600 hover:text-red-700"
                                        @click="handleRemoveFromCart(index)"
                                    >
                                        Удалить
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <div class="mt-6 space-y-3 border-t border-gray-200 pt-6">
                        <div class="flex items-center justify-between text-lg font-bold">
                            <span>Итого:</span>
                            <span :style="{ color: colors.accent[600] }">{{ formatPrice(totalPrice) }}</span>
                        </div>
                        
                        <button
                            class="w-full rounded-xl py-3 text-base font-semibold text-white shadow-sm transition-all hover:shadow-md"
                            :style="{ backgroundColor: colors.accent[500] }"
                            @click="$emit('checkout')"
                        >
                            Оформить заказ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { computed } from 'vue';
import { useCart } from '../composables/useCart.js';
import { useTheme } from '../composables/useTheme.js';

const { theme: colors } = useTheme();

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close', 'update', 'checkout']);

// Используем глобальное состояние корзины
const { cart, removeFromCart, updateQuantity } = useCart();

const getDiscountedPrice = (item) => {
    if (!item || item.discount_type === 'none' || !item.discount_value) {
        return item?.price || 0;
    }

    if (item.discount_type === 'percent') {
        return item.price - (item.price * item.discount_value / 100);
    }

    if (item.discount_type === 'fixed') {
        return Math.max(0, item.price - item.discount_value);
    }

    return item.price;
};

const getMaxQuantityForItem = (item) => {
    if (item.selectedSize && item.variants) {
        const variant = item.variants.find(v => v.size === item.selectedSize);
        return variant ? variant.quantity : 0;
    }
    return item.quantity || 0;
};

const handleRemoveFromCart = (index) => {
    removeFromCart(index);
    emit('update');
};

const increaseQuantity = (index) => {
    const item = cart.value[index];
    if (item && item.cartQuantity < getMaxQuantityForItem(item)) {
        updateQuantity(index, item.cartQuantity + 1);
        emit('update');
    }
};

const decreaseQuantity = (index) => {
    const item = cart.value[index];
    if (item) {
        if (item.cartQuantity > 1) {
            updateQuantity(index, item.cartQuantity - 1);
            emit('update');
        } else {
            handleRemoveFromCart(index);
        }
    }
};

const totalPrice = computed(() => {
    return cart.value.reduce((sum, item) => {
        const price = getDiscountedPrice(item);
        return sum + (price * item.cartQuantity);
    }, 0);
});

const formatPrice = (value) => {
    try {
        return new Intl.NumberFormat('ru-RU', {
            style: 'currency',
            currency: 'RUB',
            maximumFractionDigits: 0,
        }).format(value ?? 0);
    } catch (error) {
        return `${value} ₽`;
    }
};
</script>

<style scoped>
.cart-enter-active,
.cart-leave-active {
    transition: opacity 0.3s ease;
}

.cart-enter-from,
.cart-leave-to {
    opacity: 0;
}

.cart-enter-active > div,
.cart-leave-active > div {
    transition: transform 0.3s ease;
}

.cart-enter-from > div,
.cart-leave-to > div {
    transform: translateX(100%);
}
</style>
