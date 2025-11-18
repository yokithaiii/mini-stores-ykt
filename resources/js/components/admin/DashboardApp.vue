<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-green-50/20">
        <div class="mx-auto max-w-7xl space-y-6">
            <!-- Заголовок -->
            <section class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: colors.primary[600] }">
                            Сводка
                        </p>
                        <h2 class="mt-2 text-3xl font-bold text-gray-900">Обзор бизнес-показателей</h2>
                        <p class="mt-2 text-sm text-gray-600">
                            Актуальная статистика по категориям и товарам, заказам и статусам.
                        </p>
                    </div>
                    <button
                        :disabled="isRefreshing"
                        class="rounded-xl px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                        :style="{ 
                            backgroundColor: isRefreshing ? colors.neutral[400] : colors.primary[500],
                            ':hover': { backgroundColor: colors.primary[600] }
                        }"
                        type="button"
                        @click="refreshAll"
                    >
                        <span v-if="!isRefreshing">Обновить данные</span>
                        <span v-else>Обновляем…</span>
                    </button>
                </div>
            </section>

            <!-- Карточки статистики -->
            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <article
                    v-for="card in dashboardCards"
                    :key="card.label"
                    class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition-all hover:shadow-md"
                >
                    <div 
                        class="absolute right-0 top-0 h-24 w-24 translate-x-8 -translate-y-8 rounded-full opacity-10 transition-transform group-hover:scale-110"
                        :style="{ backgroundColor: getCardColor(card.color, 500) }"
                    ></div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ card.label }}</p>
                    <p 
                        class="mt-4 text-4xl font-bold"
                        :style="{ color: getCardColor(card.color, 600) }"
                    >
                        {{ card.value }}
                    </p>
                    <p class="mt-2 text-sm text-gray-600">{{ card.hint }}</p>
                </article>
            </section>

            <!-- Секции с данными -->
            <section class="grid gap-6 lg:grid-cols-2">
                <!-- Популярные категории -->
                <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Популярные категории</h3>
                            <p class="text-sm text-gray-600">Топ по количеству товаров</p>
                        </div>
                        <a
                            class="text-sm font-semibold transition-colors hover:underline"
                            :style="{ color: colors.primary[600] }"
                            href="/admin/categories"
                        >
                            Все категории →
                        </a>
                    </div>

                    <ul class="mt-6 space-y-2">
                        <li
                            v-for="category in topCategories"
                            :key="category.id"
                            class="flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3 text-sm font-medium transition-colors hover:bg-gray-100"
                        >
                            <span class="text-gray-900">{{ category.name }}</span>
                            <span 
                                class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                                :style="{ backgroundColor: colors.accent[500] }"
                            >
                                Товаров: {{ countProductsByCategory(category.id) }}
                            </span>
                        </li>
                        <li
                            v-if="!topCategories.length && !loading.categories"
                            class="rounded-xl bg-gray-50 px-4 py-3 text-center text-sm text-gray-500"
                        >
                            Нет данных по категориям.
                        </li>
                        <li
                            v-if="loading.categories"
                            class="rounded-xl bg-gray-50 px-4 py-3 text-center text-sm text-gray-500"
                        >
                            Загружаем категории…
                        </li>
                    </ul>
                </article>

                <!-- Недавно добавленные товары -->
                <article class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Недавно добавленные товары</h3>
                            <p class="text-sm text-gray-600">Последние 5 записей</p>
                        </div>
                        <a
                            class="text-sm font-semibold transition-colors hover:underline"
                            :style="{ color: colors.primary[600] }"
                            href="/admin/products"
                        >
                            Все товары →
                        </a>
                    </div>

                    <ul class="mt-6 space-y-2">
                        <li
                            v-for="product in lastProducts"
                            :key="product.id"
                            class="rounded-xl bg-gray-50 px-4 py-3 text-sm transition-colors hover:bg-gray-100"
                        >
                            <p class="font-semibold text-gray-900">{{ product.name }}</p>
                            <p class="mt-1 text-xs text-gray-600">
                                {{ categoryMap[product.category_id] ?? '—' }} • 
                                <span class="font-semibold" :style="{ color: colors.accent[600] }">
                                    {{ formatPrice(product.price) }}
                                </span>
                            </p>
                        </li>
                        <li
                            v-if="!lastProducts.length && !loading.products"
                            class="rounded-xl bg-gray-50 px-4 py-3 text-center text-sm text-gray-500"
                        >
                            Товары ещё не добавлены.
                        </li>
                        <li
                            v-if="loading.products"
                            class="rounded-xl bg-gray-50 px-4 py-3 text-center text-sm text-gray-500"
                        >
                            Загружаем товары…
                        </li>
                    </ul>
                </article>
            </section>
        </div>
    </div>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, reactive, ref } from 'vue';
import { themeConfig } from '../../config/theme.js';

// Цвета из конфигурации
const colors = themeConfig;

const stores = ref([]);
const categories = ref([]);
const products = ref([]);
const orders = ref([]);

const loading = reactive({
    stores: false,
    categories: false,
    products: false,
    orders: false,
});

const handleResponseData = (response) => response?.data?.data ?? response?.data ?? [];

const fetchStores = async () => {
    loading.stores = true;
    try {
        const response = await axios.get('/api/stores');
        stores.value = handleResponseData(response);
    } catch (error) {
        console.error(error);
    } finally {
        loading.stores = false;
    }
};

const fetchCategories = async () => {
    loading.categories = true;
    try {
        const response = await axios.get('/api/categories');
        categories.value = handleResponseData(response);
    } catch (error) {
        console.error(error);
    } finally {
        loading.categories = false;
    }
};

const fetchProducts = async () => {
    loading.products = true;
    try {
        const response = await axios.get('/api/products');
        products.value = handleResponseData(response);
    } catch (error) {
        console.error(error);
    } finally {
        loading.products = false;
    }
};

const fetchOrders = async () => {
    loading.orders = true;
    try {
        const response = await axios.get('/api/orders');
        orders.value = handleResponseData(response);
    } catch (error) {
        console.error(error);
    } finally {
        loading.orders = false;
    }
};

const refreshAll = async () => {
    await Promise.all([fetchStores(), fetchCategories(), fetchProducts(), fetchOrders()]);
};

const storeMap = computed(() =>
    stores.value.reduce((acc, store) => {
        acc[store.id] = store.name;
        return acc;
    }, {})
);

const categoryMap = computed(() =>
    categories.value.reduce((acc, category) => {
        acc[category.id] = category.name;
        return acc;
    }, {})
);

// Функция для получения цвета карточки
const getCardColor = (colorName, shade = 500) => {
    const colorMap = {
        primary: colors.primary[shade],
        accent: colors.accent[shade],
        success: colors.accent[shade], // зеленый
        gray: colors.neutral[shade],
        yellow: shade === 500 ? '#f59e0b' : '#d97706', // amber
        warning: shade === 500 ? '#f59e0b' : '#d97706',
        error: shade === 500 ? '#ef4444' : '#dc2626',
        info: colors.primary[shade],
    };
    
    return colorMap[colorName] || colors.primary[shade];
};

const dashboardCards = computed(() => [
    {
        label: 'Категории',
        value: loading.categories ? '…' : categories.value.length,
        hint: 'Все категории, включая дочерние',
        color: 'gray',
    },
    {
        label: 'Товары',
        value: loading.products ? '…' : products.value.length,
        hint: 'Доступные SKU в системе',
        color: 'gray',
    },
    {
        label: 'Заказы',
        value: loading.orders ? '…' : orders.value.length,
        hint: 'Всего заказов в системе',
        color: 'primary',
    },
    {
        label: 'Ожидают обработки',
        value: loading.orders ? '…' : orders.value.filter(o => o.status === 'pending').length,
        hint: 'Новые заказы требуют внимания',
        color: 'yellow',
    },
    {
        label: 'Подтверждено',
        value: loading.orders ? '…' : orders.value.filter(o => o.status === 'confirmed').length,
        hint: 'Успешно обработанные заказы',
        color: 'success',
    },
]);

const topCategories = computed(() => categories.value.slice(0, 5));
const lastProducts = computed(() => products.value.slice(-5).reverse());

const countProductsByCategory = (categoryId) =>
    products.value.filter((product) => product.category_id === categoryId).length;

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

const isRefreshing = computed(() => loading.stores || loading.categories || loading.products || loading.orders);

onMounted(() => {
    refreshAll();
});
</script>

