<template>
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Клиенты</h1>
            <p class="mt-2 text-sm text-gray-600">Список зарегистрированных клиентов и их заказы</p>
        </div>

        <!-- Скелетон при загрузке -->
        <div v-if="loading" class="space-y-4">
            <div v-for="i in 5" :key="i" class="rounded-2xl bg-white p-6 shadow-sm">
                <div class="h-6 w-48 animate-pulse rounded bg-gray-200"></div>
                <div class="mt-3 h-4 w-32 animate-pulse rounded bg-gray-200"></div>
            </div>
        </div>

        <!-- Список клиентов -->
        <div v-else-if="customers.length > 0" class="space-y-4">
            <div
                v-for="customer in customers"
                :key="customer.id"
                class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ customer.name ?? 'Нет имени' }}</h3>
                                <p class="text-sm text-gray-600">{{ customer.phone }}</p>
                            </div>
                            <button
                                class="ml-auto rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                                @click="viewCustomer(customer)"
                            >
                                Подробнее
                            </button>
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs font-medium text-gray-600">Заказов</p>
                                <p class="mt-1 text-2xl font-bold text-gray-900">{{ customer.orders_count }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs font-medium text-gray-600">Зарегистрирован</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">{{ formatDate(customer.created_at) }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs font-medium text-gray-600">Последний заказ</p>
                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ customer.orders && customer.orders.length > 0 ? formatDate(customer.orders[0].created_at) : '—' }}
                                </p>
                            </div>
                        </div>

                        <!-- Последние заказы -->
                        <div v-if="customer.orders && customer.orders.length > 0" class="mt-4">
                            <p class="mb-2 text-sm font-semibold text-gray-700">Последние заказы:</p>
                            <div class="space-y-2">
                                <div
                                    v-for="order in customer.orders.slice(0, 3)"
                                    :key="order.id"
                                    class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm"
                                >
                                    <span class="text-gray-600">Заказ #{{ String(order.id).slice(0, 8) }}</span>
                                    <span class="font-semibold text-gray-900">{{ formatPrice(order.total_price) }}</span>
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="{
                                            'bg-yellow-100 text-yellow-700': order.status === 'pending',
                                            'bg-green-100 text-green-700': order.status === 'confirmed',
                                            'bg-red-100 text-red-700': order.status === 'cancelled',
                                        }"
                                    >
                                        {{ getStatusLabel(order.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Пусто -->
        <div v-else class="rounded-2xl bg-white p-12 text-center shadow-sm ring-1 ring-gray-900/5">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <p class="mt-4 text-gray-500">Клиентов пока нет</p>
        </div>

        <!-- Модалка детальной информации -->
        <transition name="fade">
            <div
                v-if="selectedCustomer"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 backdrop-blur-sm"
                @click.self="selectedCustomer = null"
            >
                <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900">Информация о клиенте</h2>
                        <button
                            class="text-gray-600 hover:text-gray-900"
                            @click="selectedCustomer = null"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <div class="rounded-xl bg-gray-50 p-6">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Имя</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ selectedCustomer.name ?? 'Нет имени' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Телефон</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ selectedCustomer.phone }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Всего заказов</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ selectedCustomer.orders_count }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Дата регистрации</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ formatDate(selectedCustomer.created_at) }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="selectedCustomer.orders && selectedCustomer.orders.length > 0">
                            <h3 class="mb-3 text-lg font-bold text-gray-900">Все заказы</h3>
                            <div class="space-y-4">
                                <div
                                    v-for="order in selectedCustomer.orders"
                                    :key="order.id"
                                    class="rounded-xl border border-gray-200 bg-white p-4"
                                >
                                    <div class="mb-3 flex items-center justify-between border-b border-gray-200 pb-3">
                                        <div>
                                            <p class="font-semibold text-gray-900">Заказ #{{ String(order.id).slice(0, 8) }}</p>
                                            <p class="text-sm text-gray-600">{{ formatDate(order.created_at) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-gray-900">{{ formatPrice(order.total_price) }}</p>
                                            <span
                                                class="inline-block rounded-full px-3 py-1 text-xs font-semibold"
                                                :class="{
                                                    'bg-yellow-100 text-yellow-700': order.status === 'pending',
                                                    'bg-green-100 text-green-700': order.status === 'confirmed',
                                                    'bg-red-100 text-red-700': order.status === 'cancelled',
                                                }"
                                            >
                                                {{ getStatusLabel(order.status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Товары в заказе -->
                                    <div v-if="order.items && order.items.length > 0" class="space-y-2">
                                        <p class="text-sm font-semibold text-gray-700">Товары:</p>
                                        <div
                                            v-for="item in order.items"
                                            :key="item.id"
                                            class="flex items-center gap-3 rounded-lg bg-gray-50 p-3"
                                        >
                                            <div v-if="item.product && item.product.image" class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200">
                                                <img :src="item.product.image" :alt="item.product.name" class="h-full w-full object-cover" />
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ item.product ? item.product.name : 'Товар удален' }}
                                                </p>
                                                <p class="text-xs text-gray-600">
                                                    {{ item.size ? `Размер: ${item.size} • ` : '' }}{{ item.quantity }} шт. × {{ formatPrice(item.price) }}
                                                </p>
                                            </div>
                                            <p class="text-sm font-bold text-gray-900">
                                                {{ formatPrice(item.price * item.quantity) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(true);
const customers = ref([]);
const selectedCustomer = ref(null);

const loadCustomers = async () => {
    loading.value = true;
    try {
        const token = localStorage.getItem('admin_token');
        const response = await axios.get('/api/customers', {
            headers: { Authorization: `Bearer ${token}` },
        });
        customers.value = response.data;
    } catch (err) {
        console.error('Failed to load customers', err);
    } finally {
        loading.value = false;
    }
};

const viewCustomer = async (customer) => {
    try {
        const token = localStorage.getItem('admin_token');
        const response = await axios.get(`/api/customers/${customer.id}`, {
            headers: { Authorization: `Bearer ${token}` },
        });
        selectedCustomer.value = response.data;
    } catch (err) {
        console.error('Failed to load customer details', err);
        selectedCustomer.value = customer; // Fallback к базовым данным
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
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

const getStatusLabel = (status) => {
    const labels = {
        pending: 'В обработке',
        confirmed: 'Подтвержден',
        cancelled: 'Отменен',
    };
    return labels[status] || status;
};

onMounted(() => {
    loadCustomers();
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
