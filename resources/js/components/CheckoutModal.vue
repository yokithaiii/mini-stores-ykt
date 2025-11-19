<template>
    <transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 backdrop-blur-sm"
            @click.self="$emit('close')"
        >
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Оформление заказа</h2>
                    <button
                        class="text-gray-600 hover:text-gray-900"
                        @click="$emit('close')"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Если не авторизован - предлагаем войти -->
                <div v-if="!isLoggedIn" class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm font-semibold text-blue-900 mb-3">
                        Войдите, чтобы оформить заказ быстрее
                    </p>
                    <button
                        class="w-full rounded-xl py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:shadow-md"
                        :style="{ backgroundColor: colors.primary[500] }"
                        @click="$emit('show-auth')"
                    >
                        Войти
                    </button>
                    <p class="mt-3 text-center text-xs text-gray-600">
                        или продолжите как гость
                    </p>
                </div>

                <!-- Форма оформления -->
                <form @submit.prevent="submitOrder" class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">
                            Имя <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="Ваше имя"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">
                            Телефон <span class="text-red-500">*</span>
                        </label>
                        <input
                            ref="phoneInput"
                            v-model="form.phone"
                            type="tel"
                            required
                            placeholder="+7 (999) 123-45-67"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>

                    <!-- Список товаров -->
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Ваш заказ</h3>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <div
                                v-for="item in items"
                                :key="`${item.id}-${item.selectedSize}`"
                                class="flex items-center gap-3 text-sm"
                            >
                                <img
                                    v-if="item.image"
                                    :src="item.image"
                                    :alt="item.name"
                                    class="h-12 w-12 rounded-lg object-cover"
                                />
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ item.name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ item.selectedSize ? `Размер: ${item.selectedSize} • ` : '' }}{{ item.cartQuantity }} шт.
                                    </p>
                                </div>
                                <p class="font-semibold text-gray-900">{{ formatPrice(getItemTotal(item)) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Итого -->
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between text-lg font-bold">
                            <span>Итого:</span>
                            <span :style="{ color: colors.accent[600] }">{{ formatPrice(totalPrice) }}</span>
                        </div>
                    </div>

                    <!-- Кнопка оформления -->
                    <button
                        type="submit"
                        class="w-full rounded-xl py-4 text-lg font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                        :style="{ backgroundColor: loading ? colors.neutral[400] : colors.accent[500] }"
                        :disabled="loading"
                    >
                        {{ loading ? 'Оформляем...' : 'Оформить заказ' }}
                    </button>

                    <p v-if="error" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600">
                        {{ error }}
                    </p>
                </form>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import axios from 'axios';
import { usePhoneMask } from '../composables/usePhoneMask.js';
import { useToast } from '../composables/useToast.js';
import { normalizePhone } from '../utils/phoneUtils.js';
import { useTheme } from '../composables/useTheme.js';

const { theme: colors } = useTheme();
const phoneInput = ref(null);
const { success: showSuccess, error: showError } = useToast();

const props = defineProps({
    show: Boolean,
    items: Array,
    totalPrice: Number,
    isLoggedIn: Boolean,
    customerName: String,
    customerPhone: String,
});

const emit = defineEmits(['close', 'success', 'show-auth']);

const form = reactive({
    name: '',
    phone: '',
});

const loading = ref(false);
const error = ref('');

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

const getItemTotal = (item) => {
    return getDiscountedPrice(item) * item.cartQuantity;
};

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

const submitOrder = async () => {
    error.value = '';
    loading.value = true;

    try {
        const phone = localStorage.getItem('customer_phone');
        const headers = phone ? { 'X-Customer-Phone': phone } : {};

        const orderData = {
            customer_name: form.name,
            customer_phone: normalizePhone(form.phone),
            items: props.items.map(item => ({
                product_id: item.id,
                quantity: item.cartQuantity,
                size: item.selectedSize || null,
            })),
        };

        await axios.post('/api/orders', orderData, { headers });

        showSuccess('Заказ успешно оформлен! Мы свяжемся с вами в ближайшее время.', 5000);
        emit('success');
        emit('close');
    } catch (err) {
        const errorMsg = err.response?.data?.error || 'Ошибка при оформлении заказа';
        error.value = errorMsg;
        showError(errorMsg);
    } finally {
        loading.value = false;
    }
};

// Инициализируем маску телефона
usePhoneMask(phoneInput);

// Автозаполнение для авторизованных пользователей
watch(() => props.show, (newVal) => {
    if (newVal) {
        if (props.isLoggedIn) {
            form.name = props.customerName || '';
            form.phone = props.customerPhone || '';
        } else {
            form.name = '';
            form.phone = '';
        }
        error.value = '';
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
