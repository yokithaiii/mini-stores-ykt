<template>
    <div class="space-y-6">
        <section class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: colors.primary[600] }">Товары</p>
                    <h2 class="mt-2 text-3xl font-bold text-gray-900">Каталог товаров</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Добавляйте новые позиции и управляйте существующими.
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
                    <router-link
                        to="/admin/products/create"
                        class="rounded-xl px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:shadow-md"
                        :style="{ backgroundColor: colors.primary[500] }"
                    >
                        Новый товар
                    </router-link>
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
            <div v-if="loading.products" class="rounded-xl bg-gray-50 px-4 py-4 text-center text-sm text-gray-500">
                Загружаем товары…
            </div>
            <div v-else-if="!products.length" class="rounded-xl bg-gray-50 px-4 py-4 text-center text-sm text-gray-500">
                Товаров пока нет. Добавьте новую позицию!
            </div>

            <ul v-else class="space-y-3">
                <li
                    v-for="product in products"
                    :key="product.id"
                    class="rounded-xl bg-gray-50 p-5 transition-colors hover:bg-gray-100"
                >
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex gap-4">
                            <!-- Изображение товара -->
                            <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200">
                                <img 
                                    v-if="product.image" 
                                    :src="product.image" 
                                    :alt="product.name"
                                    class="h-full w-full object-cover"
                                />
                            </div>
                            
                            <div>
                                <p class="text-lg font-bold text-gray-900">{{ product.name }}</p>
                                <p class="mt-1 text-sm text-gray-600">
                                    <span v-if="product.variants && product.variants.length > 0">
                                        Кол-во: <span v-for="(variant, idx) in product.variants" :key="variant.id">{{ variant.size }}: {{ variant.quantity }}<span v-if="idx < product.variants.length - 1">, </span></span> (всего: {{ getTotalVariantQuantity(product) }})
                                    </span>
                                    <span v-else>Кол-во: {{ product.quantity }}</span>
                                    • 
                                    <span v-if="product.discount_type !== 'none' && product.discount_value">
                                        <span class="line-through text-gray-400">{{ formatPrice(product.price) }}</span>
                                        <span :style="{ color: colors.accent[600] }" class="font-semibold ml-1">{{ formatPrice(getDiscountedPrice(product)) }}</span>
                                    </span>
                                    <span v-else>
                                        Цена: <span :style="{ color: colors.accent[600] }" class="font-semibold">{{ formatPrice(product.price) }}</span>
                                    </span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    Магазин: {{ storeMap[product.store_id] ?? '—' }} • Категория: {{ categoryMap[product.category_id] ?? '—' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <router-link
                                :to="`/admin/products/${product.id}/edit`"
                                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-white"
                            >
                                Изменить
                            </router-link>
                            <button
                                class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                                type="button"
                                @click="deleteProduct(product.id)"
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
                <div class="max-h-[90vh] w-full max-w-[90vw] overflow-y-auto rounded-2xl bg-white p-6 shadow-xl ring-1 ring-gray-900/10">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider" :style="{ color: colors.primary[600] }">Товары</p>
                            <h3 class="mt-2 text-2xl font-bold text-gray-900">
                                {{ form.id ? 'Редактировать товар' : 'Новый товар' }}
                            </h3>
                        </div>
                        <button class="text-sm font-semibold text-gray-600 hover:text-gray-900" type="button" @click="closeModal">
                            Закрыть
                        </button>
                    </div>

                    <form class="mt-6 space-y-5" @submit.prevent="saveProduct">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Название</label>
                            <input
                                v-model.trim="form.name"
                                class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                placeholder="Товар"
                                required
                                type="text"
                            />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Описание</label>
                            <textarea
                                v-model.trim="form.description"
                                class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                placeholder="Описание товара..."
                                rows="4"
                            ></textarea>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700">Цена (₽)</label>
                            <input
                                v-model.number="form.price"
                                class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                min="1"
                                required
                                type="number"
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
                            <label class="text-sm font-medium text-gray-700">Категория</label>
                            <select
                                v-model="form.category_id"
                                class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                required
                            >
                                <option value="" disabled>Выберите категорию</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Бренд</label>
                                <select
                                    v-model="form.brand_id"
                                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                >
                                    <option :value="null">Без бренда</option>
                                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                        {{ brand.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Пол</label>
                                <select
                                    v-model="form.gender"
                                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                >
                                    <option :value="null">Не указан</option>
                                    <option value="male">Мужской</option>
                                    <option value="female">Женский</option>
                                    <option value="unisex">Унисекс</option>
                                </select>
                            </div>
                        </div>

                        <!-- Загрузка изображения -->
                        <div>
                            <label class="text-sm font-medium text-gray-700">Изображение товара</label>
                            
                            <!-- Превью текущего изображения -->
                            <div v-if="form.image" class="mt-2 relative inline-block">
                                <img 
                                    :src="form.image" 
                                    alt="Превью" 
                                    class="h-32 w-32 rounded-xl object-cover ring-2 ring-gray-200"
                                />
                                <button
                                    type="button"
                                    class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold hover:bg-red-600"
                                    @click="removeImage"
                                >
                                    ×
                                </button>
                            </div>

                            <!-- Кнопка загрузки -->
                            <div class="mt-2">
                                <input
                                    ref="imageInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleImageUpload"
                                />
                                <button
                                    type="button"
                                    class="rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="uploadingImage"
                                    @click="$refs.imageInput.click()"
                                >
                                    {{ uploadingImage ? 'Загружаем...' : form.image ? 'Изменить изображение' : 'Загрузить изображение' }}
                                </button>
                                <p class="mt-1 text-xs text-gray-500">
                                    JPG, PNG, GIF, WEBP до 5MB
                                </p>
                            </div>
                        </div>

                        <!-- Дополнительные изображения -->
                        <div>
                            <label class="text-sm font-medium text-gray-700">Дополнительные изображения</label>
                            <div v-if="form.images.length > 0" class="mt-2 flex flex-wrap gap-2">
                                <div
                                    v-for="(img, index) in form.images"
                                    :key="index"
                                    class="relative"
                                >
                                    <img :src="img" alt="Доп. фото" class="h-20 w-20 rounded-lg object-cover ring-2 ring-gray-200" />
                                    <button
                                        type="button"
                                        class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white hover:bg-red-600"
                                        @click="removeAdditionalImage(index)"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="mt-2 rounded-xl border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                                @click="$refs.additionalImageInput.click()"
                            >
                                + Добавить изображение
                            </button>
                            <input
                                ref="additionalImageInput"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleAdditionalImageUpload"
                            />
                        </div>

                        <!-- Скидка -->
                        <div class="border-t border-gray-200 pt-4">
                            <label class="text-sm font-semibold text-gray-900">Скидка</label>
                            <div class="mt-2 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="text-xs text-gray-600">Тип скидки</label>
                                    <select
                                        v-model="form.discount_type"
                                        class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    >
                                        <option value="none">Без скидки</option>
                                        <option value="percent">Процент (%)</option>
                                        <option value="fixed">Фиксированная сумма (₽)</option>
                                    </select>
                                </div>
                                <div v-if="form.discount_type !== 'none'">
                                    <label class="text-xs text-gray-600">Значение</label>
                                    <input
                                        v-model.number="form.discount_value"
                                        type="number"
                                        min="0"
                                        :max="form.discount_type === 'percent' ? 100 : undefined"
                                        class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                        :placeholder="form.discount_type === 'percent' ? '10' : '100'"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Размеры (варианты) - ОБЯЗАТЕЛЬНО -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-semibold text-gray-900">
                                    Размеры <span class="text-red-500">*</span>
                                </label>
                                <button
                                    type="button"
                                    class="rounded-lg border border-gray-300 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-gray-50"
                                    @click="addVariant"
                                >
                                    + Добавить размер
                                </button>
                            </div>
                            <div v-if="form.variants.length > 0" class="mt-2 space-y-2">
                                <div
                                    v-for="(variant, index) in form.variants"
                                    :key="index"
                                    class="flex gap-2"
                                >
                                    <input
                                        v-model="variant.size"
                                        type="text"
                                        required
                                        placeholder="S, M, L или 40, 41, 42"
                                        class="flex-1 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    />
                                    <input
                                        v-model.number="variant.quantity"
                                        type="number"
                                        min="0"
                                        required
                                        placeholder="Кол-во"
                                        class="w-24 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    />
                                    <button
                                        type="button"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-red-200 text-red-600 transition hover:bg-red-50"
                                        @click="removeVariant(index)"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                            <p v-if="form.variants.length === 0" class="mt-2 rounded-lg bg-yellow-50 px-3 py-2 text-xs text-yellow-700">
                                ⚠️ Необходимо добавить хотя бы один размер. Если товар без размера, укажите "Универсальный" или "Без размера"
                            </p>
                            <p v-else class="mt-1 text-xs text-gray-500">
                                Для товаров без размера укажите "Универсальный" или "Без размера"
                            </p>
                        </div>

                        <!-- Характеристики -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-semibold text-gray-900">Характеристики</label>
                                <button
                                    type="button"
                                    class="rounded-lg border border-gray-300 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-gray-50"
                                    @click="addAttribute"
                                >
                                    + Добавить характеристику
                                </button>
                            </div>
                            <div v-if="form.attributes.length > 0" class="mt-2 space-y-2">
                                <div
                                    v-for="(attr, index) in form.attributes"
                                    :key="index"
                                    class="flex gap-2"
                                >
                                    <input
                                        v-model="attr.key"
                                        type="text"
                                        placeholder="Ключ (например: Цвет)"
                                        class="flex-1 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    />
                                    <input
                                        v-model="attr.value"
                                        type="text"
                                        placeholder="Значение (например: Красный)"
                                        class="flex-1 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    />
                                    <button
                                        type="button"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-red-200 text-red-600 transition hover:bg-red-50"
                                        @click="removeAttribute(index)"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
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
import { computed, onMounted, reactive, ref } from 'vue';
import { useToast } from '../../composables/useToast.js';
import { useTheme } from '../../composables/useTheme.js';

const { theme: colors } = useTheme();
const { success: showSuccess, error: showError } = useToast();

const stores = ref([]);
const categories = ref([]);
const products = ref([]);
const brands = ref([]);

const loading = reactive({
    stores: false,
    categories: false,
    products: false,
    brands: false,
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
    description: '',
    quantity: 1,
    price: 1,
    image: '',
    images: [],
    gender: null,
    brand_id: null,
    discount_type: 'none',
    discount_value: 0,
    variants: [],
    attributes: [],
    store_id: '',
    category_id: '',
});

const saving = ref(false);
const uploadingImage = ref(false);
const imageInput = ref(null);
const additionalImageInput = ref(null);

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

const fetchBrands = async () => {
    loading.brands = true;
    try {
        const response = await axios.get('/api/brands');
        brands.value = handleResponseData(response);
    } catch (error) {
        console.error(error);
    } finally {
        loading.brands = false;
    }
};

const fetchProducts = async () => {
    loading.products = true;
    try {
        const response = await axios.get('/api/products');
        products.value = handleResponseData(response);
    } catch (error) {
        alerts.error = error?.response?.data?.error ?? 'Не удалось загрузить товары.';
    } finally {
        loading.products = false;
    }
};

const refresh = async () => {
    alerts.success = '';
    alerts.error = '';
    await Promise.all([fetchStores(), fetchCategories(), fetchProducts(), fetchBrands()]);
};

const openModal = (product = null) => {
    modal.error = '';
    if (product) {
        form.id = product.id;
        form.name = product.name;
        form.description = product.description || '';
        form.quantity = product.quantity;
        form.price = product.price;
        form.image = product.image || '';
        form.images = product.images || [];
        form.gender = product.gender || null;
        form.brand_id = product.brand_id || null;
        form.discount_type = product.discount_type || 'none';
        form.discount_value = product.discount_value || 0;
        form.variants = product.variants ? product.variants.map(v => ({ size: v.size, quantity: v.quantity })) : [];
        form.attributes = product.attributes || [];
        form.store_id = product.store_id;
        form.category_id = product.category_id;
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
    form.quantity = 1;
    form.price = 1;
    form.image = '';
    form.images = [];
    form.gender = null;
    form.brand_id = null;
    form.discount_type = 'none';
    form.discount_value = 0;
    form.variants = [];
    form.attributes = [];
    form.store_id = '';
    form.category_id = '';
};

const addVariant = () => {
    form.variants.push({ size: '', quantity: 0 });
};

const removeVariant = (index) => {
    form.variants.splice(index, 1);
};

const addAttribute = () => {
    form.attributes.push({ key: '', value: '' });
};

const removeAttribute = (index) => {
    form.attributes.splice(index, 1);
};

const handleImageUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    uploadingImage.value = true;
    modal.error = '';

    try {
        const formData = new FormData();
        formData.append('image', file);

        const response = await axios.post('/api/images/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        form.image = response.data.url;
    } catch (error) {
        modal.error = error?.response?.data?.error ?? 'Не удалось загрузить изображение';
    } finally {
        uploadingImage.value = false;
        if (imageInput.value) {
            imageInput.value.value = '';
        }
    }
};

const removeImage = () => {
    form.image = '';
};

const handleAdditionalImageUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    uploadingImage.value = true;
    modal.error = '';

    try {
        const formData = new FormData();
        formData.append('image', file);

        const response = await axios.post('/api/images/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        form.images.push(response.data.url);
    } catch (error) {
        modal.error = error?.response?.data?.error ?? 'Не удалось загрузить изображение';
    } finally {
        uploadingImage.value = false;
        if (additionalImageInput.value) {
            additionalImageInput.value.value = '';
        }
    }
};

const removeAdditionalImage = (index) => {
    form.images.splice(index, 1);
};

const saveProduct = async () => {
    modal.error = '';
    alerts.success = '';
    alerts.error = '';
    
    // Валидация размеров
    const validVariants = form.variants.filter(v => v.size && v.size.trim());
    if (validVariants.length === 0) {
        modal.error = 'Необходимо добавить хотя бы один размер';
        return;
    }
    
    saving.value = true;

    const payload = {
        name: form.name,
        description: form.description || null,
        quantity: 0, // Теперь всегда 0, используем только варианты
        price: Number(form.price),
        image: form.image || null,
        images: form.images,
        gender: form.gender,
        brand_id: form.brand_id,
        discount_type: form.discount_type,
        discount_value: form.discount_type !== 'none' ? Number(form.discount_value) : null,
        attributes: form.attributes.filter(attr => attr.key && attr.value),
        variants: validVariants,
        store_id: form.store_id,
        category_id: form.category_id,
    };

    try {
        if (form.id) {
            await axios.post(`/api/products/${form.id}`, payload);
            alerts.success = 'Товар обновлён';
        } else {
            await axios.post('/api/products', payload);
            alerts.success = 'Товар создан';
        }
        await fetchProducts();
        closeModal();
    } catch (error) {
        modal.error = error?.response?.data?.error ?? 'Ошибка при сохранении товара.';
    } finally {
        saving.value = false;
    }
};

const deleteProduct = async (id) => {
    alerts.success = '';
    alerts.error = '';
    if (!confirm('Удалить товар?')) {
        return;
    }
    try {
        await axios.delete(`/api/products/${id}`);
        showSuccess('Товар успешно удален');
        alerts.success = 'Товар удалён';
        await fetchProducts();
    } catch (error) {
        const errorMsg = error?.response?.data?.error ?? 'Не удалось удалить товар.';
        alerts.error = errorMsg;
        showError(errorMsg);
    }
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

const getDiscountedPrice = (product) => {
    if (!product || product.discount_type === 'none' || !product.discount_value) {
        return product?.price ?? 0;
    }
    
    if (product.discount_type === 'percent') {
        return product.price - (product.price * product.discount_value / 100);
    } else if (product.discount_type === 'fixed') {
        return Math.max(0, product.price - product.discount_value);
    }
    
    return product.price;
};

const getTotalVariantQuantity = (product) => {
    if (!product.variants || product.variants.length === 0) {
        return product.quantity || 0;
    }
    return product.variants.reduce((sum, variant) => sum + (variant.quantity || 0), 0);
};

onMounted(async () => {
    await refresh();
});
</script>

