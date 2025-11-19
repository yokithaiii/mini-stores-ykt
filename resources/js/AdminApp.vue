<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-green-50/20 antialiased">
        <div class="min-h-screen w-full px-4 py-8">
            <div class="mx-auto w-full max-w-7xl space-y-6">
                <!-- Шапка -->
                <header class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-blue-600">{{ storeName }}</p>
                            <h1 class="mt-2 text-2xl font-bold text-gray-900">Админ панель</h1>
                            <p class="mt-1 text-sm text-gray-600">
                                Управляйте своим магазином, категориями и товарами.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 px-5 py-3 text-white shadow-sm">
                            <div>
                                <p class="text-sm font-semibold">{{ userRole }}</p>
                                <p class="text-sm font-semibold">{{ userName }}</p>
                                <p class="text-xs text-blue-100">{{ userEmail }}</p>
                            </div>
                            <button
                                class="inline-flex items-center rounded-lg bg-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/30"
                                @click="logout"
                            >
                                Выйти
                            </button>
                        </div>
                    </div>

                    <!-- Навигация -->
                    <div class="mt-6 flex flex-wrap gap-2">
                        <a
                            href="/"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                        >
                            ← Магазин
                        </a>
                        <router-link
                            to="/admin"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold transition"
                            :class="$route.path === '/admin' ? 'bg-blue-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        >
                            Дашборд
                        </router-link>
                        <router-link
                            to="/admin/categories"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold transition"
                            :class="$route.path === '/admin/categories' ? 'bg-blue-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        >
                            Категории
                        </router-link>
                        <router-link
                            to="/admin/products"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold transition"
                            :class="$route.path === '/admin/products' ? 'bg-blue-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        >
                            Товары
                        </router-link>
                        <router-link
                            to="/admin/brands"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold transition"
                            :class="$route.path === '/admin/brands' ? 'bg-blue-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        >
                            Бренды
                        </router-link>
                        <router-link
                            to="/admin/orders"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold transition"
                            :class="$route.path === '/admin/orders' ? 'bg-blue-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        >
                            Заказы
                        </router-link>
                        <router-link
                            to="/admin/customers"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold transition"
                            :class="$route.path === '/admin/customers' ? 'bg-blue-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        >
                            Клиенты
                        </router-link>
                        <router-link
                            to="/admin/settings"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold transition"
                            :class="$route.path === '/admin/settings' ? 'bg-blue-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        >
                            Настройки
                        </router-link>
                    </div>
                </header>

                <!-- Контент -->
                <main class="space-y-6">
                    <router-view />
                </main>
            </div>
        </div>
        <ToastContainer />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import ToastContainer from './components/ToastContainer.vue';
import { useTheme } from './composables/useTheme.js';

const router = useRouter();
const { loadTheme } = useTheme();

const userName = ref('—');
const userEmail = ref('—');
const userRole = ref('—');
const storeName = ref('Mini Stores');

const loadStoreSettings = async () => {
    try {
        const response = await axios.get('/api/settings');
        storeName.value = response.data.store_name || 'Mini Stores';
    } catch (err) {
        console.error('Failed to load store settings', err);
    }
};

const loadUser = async () => {
    try {
        const token = localStorage.getItem('admin_token');
        if (!token) {
            router.push('/auth');
            return;
        }

        const response = await axios.get('/api/auth/me', {
            headers: { Authorization: `Bearer ${token}` },
        });

        const user = response.data;
        const fullName = [user.firstname, user.lastname].filter(Boolean).join(' ').trim();
        userName.value = fullName || user.email || 'Администратор';
        userEmail.value = user.email || '—';
        userRole.value = 'Админ';
    } catch (error) {
        console.error('Failed to load user', error);
        localStorage.removeItem('admin_token');
        router.push('/auth');
    }
};

const logout = () => {
    localStorage.removeItem('admin_token');
    router.push('/auth');
};

onMounted(async () => {
    // Загружаем тему первым делом
    await loadTheme();
    
    loadStoreSettings();
    loadUser();
});
</script>
