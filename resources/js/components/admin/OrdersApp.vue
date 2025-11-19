<template>
    <div class="space-y-6">
        <section class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: colors.primary[600] }">Заказы</p>
                    <h2 class="mt-2 text-3xl font-bold text-gray-900">Управление заказами</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Просматривайте и обрабатывайте заказы клиентов.
                    </p>
                </div>
                <button
                    class="rounded-xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    type="button"
                    @click="refresh"
                >
                    Обновить список
                </button>
            </div>
        </section>

        <section v-if="alerts.success" class="rounded-xl border border-green-200 bg-green-50 px-6 py-4 text-sm font-semibold text-green-700">
            {{ alerts.success }}
        </section>
        <section v-if="alerts.error" class="rounded-xl border border-red-200 bg-red-50 px-6 py-4 text-sm font-semibold text-red-600">
            {{ alerts.error }}
        </section>

        <!-- Фильтры -->
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex gap-4">
                <button
                    v-for="status in statuses"
                    :key="status.value"
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition"
                    :class="filterStatus === status.value ? 'text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    :style="filterStatus === status.value ? { backgroundColor: status.color } : {}"
                    @click="filterStatus = status.value"
                >
                    {{ status.label }}
                </button>
            </div>
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <div v-if="loading" class="rounded-xl bg-gray-50 px-4 py-4 text-center text-sm text-gray-500">
                Загружаем заказы…
            </div>
            <div v-else-if="!filteredOrders.length" class="rounded-xl bg-gray-50 px-4 py-4 text-center text-sm text-gray-500">
                Заказов пока нет.
            </div>
            <ul v-else class="space-y-4">
                <li
                    v-for="order in filteredOrders"
                    :key="order.id"
                    class="rounded-xl bg-gray-50 p-5 transition-colors hover:bg-gray-100"
                >
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-bold text-gray-900">Заказ #{{ order.id }}</h3>
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                                    :style="{ backgroundColor: getStatusColor(order.status) }"
                                >
                                    {{ getStatusLabel(order.status) }}
                                </span>
                            </div>
                            
                            <div class="mt-2 space-y-1 text-sm text-gray-600">
                                <p><strong>Клиент:</strong> {{ order.customer_name || '—' }}</p>
                                <p><strong>Телефон:</strong> {{ order.customer_phone || '—' }}</p>
                                <p v-if="order.customer_email"><strong>Email:</strong> {{ order.customer_email }}</p>
                                <p><strong>Дата:</strong> {{ formatDate(order.created_at) }}</p>
                            </div>

                            <div class="mt-2 space-y-2">
                                <p class="text-sm font-semibold text-gray-700">Товары:</p>
                                <ul class="space-y-1">
                                    <li
                                        v-for="item in order.items"
                                        :key="item.id"
                                        class="text-sm text-gray-600"
                                    >
                                        - {{ item.product?.name || 'Товар удален' }}
                                        <span v-if="item.size" class="text-xs">({{ item.size }})</span>
                                        × {{ item.quantity }} = {{ formatPrice(item.price * item.quantity) }}
                                    </li>
                                </ul>
                            </div>

                            <p class="mt-3 text-lg font-bold" :style="{ color: colors.accent[600] }">
                                Итого: {{ formatPrice(order.total_price) }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-2">
                            <button
                                v-if="order.status === 'pending'"
                                class="rounded-lg px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90"
                                :style="{ backgroundColor: colors.accent[500] }"
                                @click="updateStatus(order.id, 'confirmed')"
                            >
                                Подтвердить
                            </button>
                            <button
                                v-if="order.status === 'pending'"
                                class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                                @click="updateStatus(order.id, 'cancelled')"
                            >
                                Отменить
                            </button>
                            <button
                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-white"
                                @click="deleteOrder(order.id)"
                            >
                                Удалить
                            </button>
                        </div>
                    </div>
                </li>
            </ul>
        </section>
    </div>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, reactive, ref } from 'vue';
import { useToast } from '../../composables/useToast.js';
import { useTheme } from '../../composables/useTheme.js';

const { theme: colors } = useTheme();
const { success: showSuccess, error: showError } = useToast();

const orders = ref([]);
const loading = ref(false);
const filterStatus = ref('all');

const alerts = reactive({
    success: '',
    error: '',
});

const statuses = computed(() => [
    { value: 'all', label: 'Все', color: colors.value.neutral[500] },
    { value: 'pending', label: 'Ожидают', color: colors.value.primary[500] },
    { value: 'confirmed', label: 'Подтверждены', color: colors.value.accent[500] },
    { value: 'cancelled', label: 'Отменены', color: colors.value.error },
]);

const handleResponseData = (response) => response?.data?.data ?? response?.data ?? [];

const fetchOrders = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/orders');
        orders.value = handleResponseData(response);
    } catch (error) {
        alerts.error = error?.response?.data?.error ?? 'Не удалось загрузить заказы.';
    } finally {
        loading.value = false;
    }
};

const refresh = async () => {
    alerts.success = '';
    alerts.error = '';
    await fetchOrders();
};

const filteredOrders = computed(() => {
    if (filterStatus.value === 'all') {
        return orders.value;
    }
    return orders.value.filter(order => order.status === filterStatus.value);
});

const updateStatus = async (orderId, status) => {
    alerts.success = '';
    alerts.error = '';

    try {
        await axios.post(`/api/orders/${orderId}/status`, { status });
        const successMsg = status === 'confirmed' ? 'Заказ подтвержден' : 'Заказ отменен';
        showSuccess(successMsg);
        alerts.success = successMsg;
        await fetchOrders();
    } catch (error) {
        const errorMsg = error?.response?.data?.error ?? 'Не удалось обновить статус заказа.';
        alerts.error = errorMsg;
        showError(errorMsg);
    }
};

const deleteOrder = async (orderId) => {
    alerts.success = '';
    alerts.error = '';
    
    if (!confirm('Удалить заказ?')) {
        return;
    }

    try {
        await axios.delete(`/api/orders/${orderId}`);
        showSuccess('Заказ успешно удален');
        alerts.success = 'Заказ удален';
        await fetchOrders();
    } catch (error) {
        const errorMsg = error?.response?.data?.error ?? 'Не удалось удалить заказ.';
        alerts.error = errorMsg;
        showError(errorMsg);
    }
};

const getStatusLabel = (status) => {
    const statusObj = statuses.value.find(s => s.value === status);
    return statusObj?.label || status;
};

const getStatusColor = (status) => {
    const statusObj = statuses.value.find(s => s.value === status);
    return statusObj?.color || colors.value.neutral[500];
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

const formatDate = (dateString) => {
    try {
        return new Intl.DateTimeFormat('ru-RU', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(dateString));
    } catch (error) {
        return dateString;
    }
};

onMounted(async () => {
    await refresh();
});
</script>
