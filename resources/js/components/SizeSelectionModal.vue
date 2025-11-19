<template>
    <transition name="fade">
        <div
            v-if="show && product"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 backdrop-blur-sm"
            @click.self="$emit('close')"
        >
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Выберите размер</h2>
                    <button
                        class="text-gray-600 hover:text-gray-900"
                        @click="$emit('close')"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mb-4">
                    <div class="flex gap-3">
                        <img 
                            v-if="product.image" 
                            :src="product.image" 
                            :alt="product.name"
                            class="h-20 w-20 rounded-lg object-cover"
                        />
                        <div>
                            <h3 class="font-bold text-gray-900">{{ product.name }}</h3>
                            <p class="text-lg font-bold" :style="{ color: colors.accent[600] }">
                                {{ formatPrice(product.discounted_price || product.price) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="text-sm font-semibold text-gray-900 mb-2 block">
                        Размер <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="variant in product.variants"
                            :key="variant.id"
                            class="rounded-lg border px-4 py-2 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50"
                            :class="selectedVariant?.id === variant.id ? 'border-blue-500 bg-blue-50 text-blue-700 ring-2 ring-blue-500/20' : 'border-gray-300 text-gray-700 hover:border-gray-400'"
                            :disabled="variant.quantity === 0"
                            @click="selectedVariant = variant"
                        >
                            {{ variant.size }}
                            <span v-if="variant.quantity > 0" class="ml-1 text-xs text-gray-500">({{ variant.quantity }})</span>
                            <span v-else class="ml-1 text-xs text-red-500">(нет)</span>
                        </button>
                    </div>
                </div>

                <button
                    class="w-full rounded-xl py-3 text-base font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                    :style="{ backgroundColor: selectedVariant ? colors.primary[500] : colors.neutral[400] }"
                    :disabled="!selectedVariant"
                    @click="addToCart"
                >
                    {{ selectedVariant ? 'Добавить в корзину' : 'Выберите размер' }}
                </button>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useTheme } from '../composables/useTheme.js';

const { theme: colors } = useTheme();

const props = defineProps({
    show: Boolean,
    product: Object,
});

const emit = defineEmits(['close', 'add-to-cart']);

const selectedVariant = ref(null);

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

const addToCart = () => {
    if (selectedVariant.value) {
        emit('add-to-cart', selectedVariant.value);
        emit('close');
    }
};

// Сбрасываем выбор при открытии/закрытии
watch(() => props.show, (newVal) => {
    if (!newVal) {
        selectedVariant.value = null;
    }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
