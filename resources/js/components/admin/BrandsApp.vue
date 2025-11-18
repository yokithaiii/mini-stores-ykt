<template>
    <div class="space-y-6">
        <section class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: colors.primary[600] }">Бренды</p>
                    <h2 class="mt-2 text-3xl font-bold text-gray-900">Управление брендами</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Добавляйте и управляйте брендами товаров.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button
                        class="rounded-xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        type="button"
                        @click="refresh"
                    >
                        Обновить список
                    </button>
                    <button
                        class="rounded-xl px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:shadow-md"
                        :style="{ backgroundColor: colors.primary[500] }"
                        type="button"
                        @click="openModal()"
                    >
                        Новый бренд
                    </button>
                </div>
            </div>
        </section>

        <section v-if="alerts.success" class="rounded-xl border border-green-200 bg-green-50 px-6 py-4 text-sm font-semibold text-green-700">
            {{ alerts.success }}
        </section>
        <section v-if="alerts.error" class="rounded-xl border border-red-200 bg-red-50 px-6 py-4 text-sm font-semibold text-red-600">
            {{ alerts.error }}
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <div v-if="loading" class="rounded-xl bg-gray-50 px-4 py-4 text-center text-sm text-gray-500">
                Загружаем бренды…
            </div>
            <div v-else-if="!brands.length" class="rounded-xl bg-gray-50 px-4 py-4 text-center text-sm text-gray-500">
                Брендов пока нет. Добавьте первый!
            </div>
            <ul v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <li
                    v-for="brand in brands"
                    :key="brand.id"
                    class="rounded-xl bg-gray-50 p-5 transition-colors hover:bg-gray-100"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900">{{ brand.name }}</h3>
                            <p v-if="brand.description" class="mt-1 text-sm text-gray-600">{{ brand.description }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-white"
                                type="button"
                                @click="openModal(brand)"
                            >
                                Изменить
                            </button>
                            <button
                                class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50"
                                type="button"
                                @click="deleteBrand(brand.id)"
                            >
                                Удалить
                            </button>
                        </div>
                    </div>
                </li>
            </ul>
        </section>

        <!-- Модалка -->
        <transition name="fade">
            <div
                v-if="modal.open"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-6 backdrop-blur-sm"
                @click.self="closeModal"
            >
                <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-900/10">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: colors.primary[600] }">Бренды</p>
                            <h3 class="mt-2 text-2xl font-bold text-gray-900">
                                {{ form.id ? 'Редактировать бренд' : 'Новый бренд' }}
                            </h3>
                        </div>
                        <button class="text-sm font-semibold text-gray-600 hover:text-gray-900" type="button" @click="closeModal">
                            Закрыть
                        </button>
                    </div>

                    <form class="mt-6 space-y-5" @submit.prevent="saveBrand">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Название</label>
                            <input
                                v-model.trim="form.name"
                                class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                placeholder="Nike, Adidas, Puma..."
                                required
                                type="text"
                            />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Описание</label>
                            <textarea
                                v-model.trim="form.description"
                                class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                placeholder="Описание бренда..."
                                rows="3"
                            ></textarea>
                        </div>

                        <div class="space-y-3">
                            <button
                                :disabled="saving"
                                class="w-full rounded-xl px-5 py-3 text-base font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                                :style="{ backgroundColor: saving ? colors.neutral[400] : colors.primary[500] }"
                                type="submit"
                            >
                                <span v-if="!saving">{{ form.id ? 'Обновить' : 'Создать' }}</span>
                                <span v-else>Сохраняем…</span>
                            </button>
                            <p
                                v-if="modal.error"
                                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-600"
                            >
                                {{ modal.error }}
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';
import { themeConfig } from '../../config/theme.js';

const colors = themeConfig;

const brands = ref([]);
const loading = ref(false);
const saving = ref(false);

const alerts = reactive({
    success: '',
    error: '',
});

const modal = reactive({
    open: false,
    error: '',
});

const form = reactive({
    id: null,
    name: '',
    description: '',
});

const handleResponseData = (response) => response?.data?.data ?? response?.data ?? [];

const fetchBrands = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/brands');
        brands.value = handleResponseData(response);
    } catch (error) {
        alerts.error = error?.response?.data?.error ?? 'Не удалось загрузить бренды.';
    } finally {
        loading.value = false;
    }
};

const refresh = async () => {
    alerts.success = '';
    alerts.error = '';
    await fetchBrands();
};

const openModal = (brand = null) => {
    modal.error = '';
    if (brand) {
        form.id = brand.id;
        form.name = brand.name;
        form.description = brand.description || '';
    } else {
        resetForm();
    }
    modal.open = true;
};

const closeModal = () => {
    modal.open = false;
    modal.error = '';
    resetForm();
};

const resetForm = () => {
    form.id = null;
    form.name = '';
    form.description = '';
};

const saveBrand = async () => {
    modal.error = '';
    alerts.success = '';
    alerts.error = '';
    saving.value = true;

    const payload = {
        name: form.name,
        description: form.description || null,
    };

    try {
        if (form.id) {
            await axios.post(`/api/brands/${form.id}`, payload);
            alerts.success = 'Бренд обновлен';
        } else {
            await axios.post('/api/brands', payload);
            alerts.success = 'Бренд создан';
        }
        await fetchBrands();
        closeModal();
    } catch (error) {
        modal.error = error?.response?.data?.error ?? 'Ошибка при сохранении бренда.';
    } finally {
        saving.value = false;
    }
};

const deleteBrand = async (id) => {
    alerts.success = '';
    alerts.error = '';
    if (!confirm('Удалить бренд?')) {
        return;
    }
    try {
        await axios.delete(`/api/brands/${id}`);
        alerts.success = 'Бренд удален';
        await fetchBrands();
    } catch (error) {
        alerts.error = error?.response?.data?.error ?? 'Не удалось удалить бренд.';
    }
};

onMounted(async () => {
    await refresh();
});
</script>
