<template>
    <div class="flex min-h-screen w-full flex-col items-center justify-center bg-[#FDFDFC] px-4 py-12 text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-white">
        <div class="w-full max-w-md rounded-2xl border border-black/5 bg-white/80 p-8 shadow-2xl shadow-black/5 backdrop-blur dark:border-white/10 dark:bg-white/5">
            <div class="mb-8 text-center">
                <p class="text-sm font-medium uppercase tracking-[0.3em] text-black/40 dark:text-white/60">Admin</p>
                <h1 class="mt-2 text-3xl font-semibold">Вход в панель</h1>
                <p class="mt-2 text-base text-black/60 dark:text-white/70">
                    Авторизуйтесь с помощью корпоративной почты, чтобы продолжить.
                </p>
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <div>
                    <label class="text-sm font-medium text-black/70 dark:text-white/80" for="email">Email</label>
                    <input
                        id="email"
                        v-model.trim="form.email"
                        autocomplete="email"
                        class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-base text-black outline-none transition focus:border-black/60 dark:border-white/20 dark:bg-black/30 dark:text-white dark:focus:border-white/60"
                        placeholder="admin@example.com"
                        required
                        type="email"
                    />
                </div>

                <div>
                    <label class="text-sm font-medium text-black/70 dark:text-white/80" for="password">Пароль</label>
                    <input
                        id="password"
                        v-model.trim="form.password"
                        autocomplete="current-password"
                        class="mt-2 w-full rounded-xl border border-black/10 bg-white px-4 py-3 text-base text-black outline-none transition focus:border-black/60 dark:border-white/20 dark:bg-black/30 dark:text-white dark:focus:border-white/60"
                        placeholder="••••••••"
                        required
                        :type="showPassword ? 'text' : 'password'"
                    />
                    <button
                        class="mt-1 text-sm font-medium text-black/50 underline-offset-4 transition hover:text-black dark:text-white/70 dark:hover:text-white"
                        type="button"
                        @click="togglePassword"
                    >
                        {{ showPassword ? 'Скрыть пароль' : 'Показать пароль' }}
                    </button>
                </div>

                <div class="space-y-4">
                    <button
                        :disabled="isSubmitting"
                        class="flex w-full items-center justify-center rounded-xl bg-black px-4 py-3 text-base font-semibold text-white transition hover:bg-black/90 disabled:cursor-not-allowed disabled:bg-black/30 dark:bg-white dark:text-black dark:hover:bg-white/90"
                        type="submit"
                    >
                        <span v-if="!isSubmitting">Войти</span>
                        <svg
                            v-else
                            class="h-5 w-5 animate-spin text-white dark:text-black"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                d="M4 12a8 8 0 018-8"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-width="4"
                            />
                        </svg>
                    </button>

                    <p
                        v-if="error"
                        class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 dark:border-red-500/50 dark:bg-red-500/10 dark:text-red-200"
                    >
                        {{ error }}
                    </p>

                    <p
                        v-if="success"
                        class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 dark:border-emerald-500/50 dark:bg-emerald-500/10 dark:text-emerald-200"
                    >
                        Успешно! Перенаправляем в админку...
                    </p>
                </div>
            </form>
        </div>

        <p class="mt-10 text-sm text-black/50 dark:text-white/60">
            {{ new Date().getFullYear() }} · {{ appName }}
        </p>
    </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    adminUrl: {
        type: String,
        default: '/admin',
    },
});

const appName = computed(() => window?.APP_NAME ?? 'Mini Stores');
const form = reactive({
    email: '',
    password: '',
});

const error = ref('');
const success = ref(false);
const isSubmitting = ref(false);
const showPassword = ref(false);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const setAuthToken = (token) => {
    if (!token) {
        return;
    }
    localStorage.setItem('admin_token', token);
    axios.defaults.headers.common.Authorization = `Bearer ${token}`;
};

const checkExistingToken = async () => {
    const token = localStorage.getItem('admin_token');
    if (!token) {
        return;
    }

    try {
        await axios.get('/api/auth/me', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        
        // Токен валидный, перенаправляем в админку
        window.location.href = props.adminUrl;
    } catch (err) {
        // Токен невалидный, удаляем
        localStorage.removeItem('admin_token');
    }
};

const submit = async () => {
    error.value = '';
    success.value = false;
    isSubmitting.value = true;

    try {
        const { data } = await axios.post('/api/auth/login-email', {
            email: form.email,
            password: form.password,
        });

        setAuthToken(data?.access_token);
        success.value = true;

        setTimeout(() => {
            window.location.href = props.adminUrl;
        }, 900);
    } catch (err) {
        error.value = err?.response?.data?.error ?? 'Не удалось войти. Попробуйте снова.';
    } finally {
        isSubmitting.value = false;
    }
};

// Проверяем токен при загрузке
checkExistingToken();
</script>

