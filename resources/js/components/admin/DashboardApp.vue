<template>
    <div>
        <div class="mb-6 flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Аналитика</h1>
                <select
                    v-model="period"
                    class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                    @change="loadStatistics"
                >
                    <option :value="7">За 7 дней</option>
                    <option :value="30">За 30 дней</option>
                    <option :value="90">За 90 дней</option>
                </select>
            </div>

            <!-- Скелетон при загрузке -->
            <div v-if="loading" class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div v-for="i in 4" :key="i" class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="h-4 w-24 animate-pulse rounded bg-gray-200"></div>
                        <div class="mt-3 h-8 w-32 animate-pulse rounded bg-gray-200"></div>
                    </div>
                </div>
                <div class="h-80 animate-pulse rounded-2xl bg-white"></div>
            </div>

            <!-- Контент -->
            <div v-else-if="statistics" class="space-y-6">
                <!-- Карточки с метриками -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white shadow-sm">
                        <p class="text-sm font-medium text-blue-100">Общая выручка</p>
                        <p class="mt-2 text-3xl font-bold">{{ formatPrice(statistics.summary.total_revenue) }}</p>
                        <p class="mt-1 text-xs text-blue-100">За выбранный период</p>
                    </div>
                    
                    <div class="rounded-2xl bg-gradient-to-br from-green-500 to-green-600 p-6 text-white shadow-sm">
                        <p class="text-sm font-medium text-green-100">Заказов всего</p>
                        <p class="mt-2 text-3xl font-bold">{{ statistics.summary.total_orders }}</p>
                        <p class="mt-1 text-xs text-green-100">За выбранный период</p>
                    </div>
                    
                    <div class="rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 p-6 text-white shadow-sm">
                        <p class="text-sm font-medium text-purple-100">Средний чек</p>
                        <p class="mt-2 text-3xl font-bold">{{ formatPrice(statistics.summary.average_check) }}</p>
                        <p class="mt-1 text-xs text-purple-100">За выбранный период</p>
                    </div>
                    
                    <div class="rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 p-6 text-white shadow-sm">
                        <p class="text-sm font-medium text-orange-100">Топ товар</p>
                        <p v-if="statistics.summary.top_product" class="mt-2 text-lg font-bold truncate">
                            {{ statistics.summary.top_product.name }}
                        </p>
                        <p v-if="statistics.summary.top_product" class="text-xs text-orange-100">
                            Продано: {{ statistics.summary.top_product.sold }} шт.
                        </p>
                        <p v-else class="mt-2 text-lg text-orange-100">Нет данных</p>
                    </div>
                </div>

                <!-- Дополнительные карточки -->
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <p class="text-sm font-medium text-gray-600">Всего товаров</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ statistics.summary.total_products }}</p>
                    </div>
                    
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <p class="text-sm font-medium text-gray-600">Категорий</p>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ statistics.summary.total_categories }}</p>
                    </div>
                    
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <p class="text-sm font-medium text-gray-600">Клиентов</p>
                        <p class="mt-2 text-3xl font-bold text-blue-600">{{ statistics.summary.total_customers }}</p>
                    </div>
                    
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <p class="text-sm font-medium text-gray-600">Заказов в ожидании</p>
                        <p class="mt-2 text-3xl font-bold text-yellow-600">{{ statistics.summary.pending_orders }}</p>
                    </div>
                    
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <p class="text-sm font-medium text-gray-600">Заказов подтверждено</p>
                        <p class="mt-2 text-3xl font-bold text-green-600">{{ statistics.summary.confirmed_orders }}</p>
                    </div>
                </div>

                <!-- График продаж по дням -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <h2 class="mb-4 text-xl font-bold text-gray-900">Продажи по дням</h2>
                    <div v-if="!statistics.sales_by_day || statistics.sales_by_day.length === 0" class="py-12 text-center text-gray-400">
                        Нет данных за выбранный период
                    </div>
                    <div v-show="statistics.sales_by_day && statistics.sales_by_day.length > 0" class="h-64">
                        <canvas ref="salesChart"></canvas>
                    </div>
                </div>

                <!-- Графики в две колонки -->
                <div class="grid gap-6 lg:grid-cols-2">
                    <!-- Топ-5 товаров -->
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <h2 class="mb-4 text-xl font-bold text-gray-900">Топ-5 товаров</h2>
                        <div v-if="!statistics.top_products || statistics.top_products.length === 0" class="py-12 text-center text-gray-400">
                            Нет данных за выбранный период
                        </div>
                        <div v-show="statistics.top_products && statistics.top_products.length > 0" class="h-64">
                            <canvas ref="topProductsChart"></canvas>
                        </div>
                    </div>

                    <!-- Статусы заказов -->
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <h2 class="mb-4 text-xl font-bold text-gray-900">Статусы заказов</h2>
                        <div v-if="!statistics.orders_by_status || statistics.orders_by_status.length === 0" class="py-12 text-center text-gray-400">
                            Нет данных за выбранный период
                        </div>
                        <div v-show="statistics.orders_by_status && statistics.orders_by_status.length > 0" class="h-64">
                            <canvas ref="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Продажи по категориям -->
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <h2 class="mb-4 text-xl font-bold text-gray-900">Продажи по категориям</h2>
                    <div v-if="!statistics.sales_by_category || statistics.sales_by_category.length === 0" class="py-12 text-center text-gray-400">
                        Нет данных за выбранный период
                    </div>
                    <div v-show="statistics.sales_by_category && statistics.sales_by_category.length > 0" class="h-80">
                        <canvas ref="categoryChart"></canvas>
                    </div>
                </div>
            </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const loading = ref(true);
const statistics = ref(null);
const period = ref(30);

const salesChart = ref(null);
const topProductsChart = ref(null);
const categoryChart = ref(null);
const statusChart = ref(null);

let salesChartInstance = null;
let topProductsChartInstance = null;
let categoryChartInstance = null;
let statusChartInstance = null;

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

const loadStatistics = async () => {
    loading.value = true;
    try {
        const token = localStorage.getItem('admin_token');
        const response = await axios.get(`/api/statistics?period=${period.value}`, {
            headers: { Authorization: `Bearer ${token}` },
        });
        statistics.value = response.data;
        loading.value = false;
        
        // Ждем пока DOM обновится и элементы станут видимыми
        await nextTick();
        setTimeout(() => {
            renderCharts();
        }, 50);
    } catch (error) {
        console.error('Failed to load statistics', error);
        loading.value = false;
    }
};

const renderCharts = () => {
    if (!statistics.value) return;

    // Уничтожаем старые графики
    if (salesChartInstance) salesChartInstance.destroy();
    if (topProductsChartInstance) topProductsChartInstance.destroy();
    if (categoryChartInstance) categoryChartInstance.destroy();
    if (statusChartInstance) statusChartInstance.destroy();

    // График продаж по дням
    if (salesChart.value && statistics.value.sales_by_day && statistics.value.sales_by_day.length > 0) {
        const ctx = salesChart.value.getContext('2d');
        salesChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: statistics.value.sales_by_day.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' });
                }),
                datasets: [{
                    label: 'Выручка',
                    data: statistics.value.sales_by_day.map(item => parseFloat(item.revenue)),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => formatPrice(context.parsed.y),
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => formatPrice(value),
                        },
                    },
                },
            },
        });
    }

    // Топ-5 товаров
    if (topProductsChart.value && statistics.value.top_products && statistics.value.top_products.length > 0) {
        const ctx = topProductsChart.value.getContext('2d');
        topProductsChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: statistics.value.top_products.map(item => item.name),
                datasets: [{
                    label: 'Продано',
                    data: statistics.value.top_products.map(item => parseInt(item.sold)),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    x: { beginAtZero: true },
                },
            },
        });
    }

    // Статусы заказов
    if (statusChart.value && statistics.value.orders_by_status && statistics.value.orders_by_status.length > 0) {
        const ctx = statusChart.value.getContext('2d');
        statusChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: statistics.value.orders_by_status.map(item => item.status),
                datasets: [{
                    data: statistics.value.orders_by_status.map(item => parseInt(item.count)),
                    backgroundColor: [
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                },
            },
        });
    }

    // Продажи по категориям
    if (categoryChart.value && statistics.value.sales_by_category && statistics.value.sales_by_category.length > 0) {
        const ctx = categoryChart.value.getContext('2d');
        categoryChartInstance = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: statistics.value.sales_by_category.map(item => item.category),
                datasets: [{
                    data: statistics.value.sales_by_category.map(item => parseInt(item.sold)),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                },
            },
        });
    }
};

onMounted(() => {
    loadStatistics();
});
</script>
