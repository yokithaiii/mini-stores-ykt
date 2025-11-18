<template>
    <transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 backdrop-blur-sm"
            @click.self="$emit('close')"
        >
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">Вход</h2>
                    <button
                        class="text-gray-600 hover:text-gray-900"
                        @click="$emit('close')"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Шаг 1: Ввод телефона -->
                <div v-if="step === 1">
                    <p class="text-sm text-gray-600 mb-4">
                        Введите номер телефона для входа
                    </p>
                    <input
                        v-model="phone"
                        type="tel"
                        placeholder="+7 (999) 123-45-67"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        @keyup.enter="sendCode"
                    />
                    <button
                        class="mt-4 w-full rounded-xl py-3 text-base font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                        :style="{ backgroundColor: loading ? colors.neutral[400] : colors.primary[500] }"
                        :disabled="loading || !phone"
                        @click="sendCode"
                    >
                        {{ loading ? 'Отправляем...' : 'Получить код' }}
                    </button>
                </div>

                <!-- Шаг 2: Ввод кода -->
                <div v-if="step === 2">
                    <p class="text-sm text-gray-600 mb-4">
                        Введите код из СМС, отправленный на номер {{ phone }}
                    </p>
                    <div class="mb-2 rounded-lg bg-yellow-50 px-4 py-3 text-sm text-yellow-700">
                        <strong>Для разработки:</strong> используйте код <strong>1111</strong>
                    </div>
                    <input
                        v-model="code"
                        type="text"
                        maxlength="4"
                        placeholder="1111"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-center text-2xl font-bold text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        @keyup.enter="verifyCode"
                    />
                    <button
                        class="mt-4 w-full rounded-xl py-3 text-base font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                        :style="{ backgroundColor: loading ? colors.neutral[400] : colors.primary[500] }"
                        :disabled="loading || code.length !== 4"
                        @click="verifyCode"
                    >
                        {{ loading ? 'Проверяем...' : 'Войти' }}
                    </button>
                    <button
                        class="mt-2 w-full text-sm text-blue-600 hover:text-blue-700"
                        @click="step = 1; code = ''"
                    >
                        Изменить номер
                    </button>
                </div>

                <p v-if="error" class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600">
                    {{ error }}
                </p>
            </div>
        </div>
    </transition>
</template>

<script setup>
import axios from 'axios';
import { ref } from 'vue';
import { themeConfig } from '../config/theme.js';

const colors = themeConfig;

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(['close', 'success']);

const step = ref(1);
const phone = ref('');
const code = ref('');
const loading = ref(false);
const error = ref('');

const sendCode = async () => {
    error.value = '';
    loading.value = true;

    try {
        const response = await axios.post('/api/customer/send-code', {
            phone: phone.value,
        });

        step.value = 2;
    } catch (err) {
        error.value = err.response?.data?.error || 'Ошибка отправки кода';
    } finally {
        loading.value = false;
    }
};

const verifyCode = async () => {
    error.value = '';
    loading.value = true;

    try {
        const response = await axios.post('/api/customer/verify-code', {
            phone: phone.value,
            code: code.value,
        });

        // Сохраняем телефон в localStorage
        localStorage.setItem('customer_phone', phone.value);
        localStorage.setItem('customer_data', JSON.stringify(response.data.customer));

        emit('success', response.data.customer);
        emit('close');
        
        // Сбрасываем форму
        step.value = 1;
        phone.value = '';
        code.value = '';
    } catch (err) {
        error.value = err.response?.data?.error || 'Неверный код';
    } finally {
        loading.value = false;
    }
};
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
