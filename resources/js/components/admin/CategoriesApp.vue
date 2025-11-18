<template>
    <div class="space-y-6">
        <section class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: colors.accent[600] }">Категории</p>
                    <h2 class="mt-2 text-3xl font-bold text-gray-900">Управление категориями</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Создавайте и редактируйте структуру каталога.
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
                        :style="{ backgroundColor: colors.accent[500] }"
                        type="button"
                        @click="openModal()"
                    >
                        Новая категория
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
            <div v-if="loading.categories" class="rounded-xl bg-gray-50 px-4 py-4 text-center text-sm text-gray-500">
                Загружаем категории…
            </div>
            <div v-else-if="!categories.length" class="rounded-xl bg-gray-50 px-4 py-4 text-center text-sm text-gray-500">
                Категорий пока нет. Добавьте первую!
            </div>
            <ul v-else class="space-y-3">
                <li
                    v-for="category in categories"
                    :key="category.id"
                    class="rounded-xl bg-gray-50 p-5 transition-colors hover:bg-gray-100"
                >
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ category.name }}</p>
                            <p class="mt-1 text-sm text-gray-600">
                                Магазин: {{ storeMap[category.store_id] ?? '—' }} • Родитель: {{ category.parent?.name ?? '—' }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-white"
                                type="button"
                                @click="openModal(category)"
                            >
                                Изменить
                            </button>
                            <button
                                class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                                type="button"
                                @click="deleteCategory(category.id)"
                            >
                                Удалить
                            </button>
                        </div>
                    </div>
                </li>
            </ul>
        </section>

        <transition name="fade">
            <div
                v-if="modal.open"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-6 backdrop-blur-sm"
            >
                <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-900/10">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: colors.accent[600] }">Категории</p>
                            <h3 class="mt-2 text-2xl font-bold text-gray-900">
                                {{ form.id ? 'Редактировать категорию' : 'Новая категория' }}
                            </h3>
                        </div>
                        <button class="text-sm font-semibold text-gray-600 hover:text-gray-900" type="button" @click="closeModal">
                            Закрыть
                        </button>
                    </div>

                    <form class="mt-6 space-y-5" @submit.prevent="saveCategory">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Название</label>
                            <input
                                v-model.trim="form.name"
                                class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                placeholder="Категория"
                                required
                                type="text"
                            />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Магазин</label>
                            <select
                                v-model="form.store_id"
                                class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                required
                            >
                                <option value="" disabled>Выберите магазин</option>
                                <option v-for="store in stores" :key="store.id" :value="store.id">
                                    {{ store.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Родительская категория</label>
                            <select
                                v-model="form.category_id"
                                class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            >
                                <option value="">Без родителя</option>
                                <option v-for="category in parentOptions" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <button
                                :disabled="saving"
                                class="w-full rounded-xl px-5 py-3 text-base font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                                :style="{ backgroundColor: saving ? colors.neutral[400] : colors.accent[500] }"
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
import { computed, onMounted, reactive, ref } from 'vue';
import { themeConfig } from '../../config/theme.js';

const colors = themeConfig;

const stores = ref([]);
const categories = ref([]);

const loading = reactive({
    stores: false,
    categories: false,
});

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
    store_id: '',
    category_id: '',
});

const saving = ref(false);

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
        alerts.error = error?.response?.data?.error ?? 'Не удалось загрузить категории.';
    } finally {
        loading.categories = false;
    }
};

const refresh = async () => {
    alerts.success = '';
    alerts.error = '';
    await Promise.all([fetchStores(), fetchCategories()]);
};

const openModal = (category = null) => {
    modal.error = '';
    if (category) {
        form.id = category.id;
        form.name = category.name;
        form.store_id = category.store_id;
        form.category_id = category.category_id ?? '';
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
    form.store_id = '';
    form.category_id = '';
};

const saveCategory = async () => {
    modal.error = '';
    alerts.success = '';
    alerts.error = '';
    saving.value = true;

    const payload = {
        name: form.name,
        store_id: form.store_id || null,
        category_id: form.category_id || null,
    };

    try {
        if (form.id) {
            await axios.post(`/api/categories/${form.id}`, payload);
            alerts.success = 'Категория обновлена';
        } else {
            await axios.post('/api/categories', payload);
            alerts.success = 'Категория создана';
        }
        await fetchCategories();
        closeModal();
    } catch (error) {
        modal.error = error?.response?.data?.error ?? 'Ошибка при сохранении категории.';
    } finally {
        saving.value = false;
    }
};

const deleteCategory = async (id) => {
    alerts.success = '';
    alerts.error = '';
    if (!confirm('Удалить категорию?')) {
        return;
    }
    try {
        await axios.delete(`/api/categories/${id}`);
        alerts.success = 'Категория удалена';
        await fetchCategories();
    } catch (error) {
        alerts.error = error?.response?.data?.error ?? 'Не удалось удалить категорию.';
    }
};

const storeMap = computed(() =>
    stores.value.reduce((acc, store) => {
        acc[store.id] = store.name;
        return acc;
    }, {})
);

const parentOptions = computed(() => {
    if (!form.store_id) {
        return categories.value.filter((category) => category.id !== form.id);
    }
    return categories.value.filter(
        (category) => category.store_id === form.store_id && category.id !== form.id
    );
});

onMounted(async () => {
    await refresh();
});
</script>

