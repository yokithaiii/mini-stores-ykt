<template>
    <div>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <router-link
                    to="/admin/products"
                    class="mb-2 inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Назад к товарам
                </router-link>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ isEditMode ? 'Редактировать товар' : 'Создать товар' }}
                </h1>
            </div>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Основная информация -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <h2 class="mb-4 text-xl font-bold text-gray-900">Основная информация</h2>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Название товара <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="Например: Nike Air Max 90"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Описание</label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            placeholder="Подробное описание товара..."
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Категория <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model.number="form.category_id"
                            required
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">Выберите категорию</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Бренд</label>
                        <select
                            v-model.number="form.brand_id"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">Без бренда</option>
                            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                {{ brand.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Пол</label>
                        <select
                            v-model="form.gender"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="">Не указан</option>
                            <option value="male">Мужской</option>
                            <option value="female">Женский</option>
                            <option value="unisex">Унисекс</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Цена <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model.number="form.price"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            placeholder="0.00"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>
                </div>
            </div>

            <!-- Скидка -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <h2 class="mb-4 text-xl font-bold text-gray-900">Скидка</h2>
                
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Тип скидки</label>
                        <select
                            v-model="form.discount_type"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        >
                            <option value="none">Без скидки</option>
                            <option value="percent">Процент</option>
                            <option value="fixed">Фиксированная сумма</option>
                        </select>
                    </div>

                    <div v-if="form.discount_type !== 'none'">
                        <label class="block text-sm font-medium text-gray-700">
                            Значение скидки
                        </label>
                        <input
                            v-model.number="form.discount_value"
                            type="number"
                            min="0"
                            :step="form.discount_type === 'percent' ? '1' : '0.01'"
                            :max="form.discount_type === 'percent' ? '100' : undefined"
                            placeholder="0"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>
                </div>
            </div>

            <!-- Варианты (размеры) -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Варианты товара (размеры)</h2>
                    <button
                        type="button"
                        class="rounded-xl bg-blue-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600"
                        @click="addVariant"
                    >
                        + Добавить размер
                    </button>
                </div>

                <div v-if="form.variants.length === 0" class="rounded-xl border-2 border-dashed border-gray-300 p-8 text-center">
                    <p class="text-gray-500">Нет вариантов. Добавьте размеры для товара.</p>
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="(variant, index) in form.variants"
                        :key="index"
                        class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4"
                    >
                        <div class="flex-1">
                            <input
                                v-model="variant.size"
                                type="text"
                                placeholder="Размер (например: M, 42, XL)"
                                required
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>
                        <div class="w-32">
                            <input
                                v-model.number="variant.quantity"
                                type="number"
                                min="0"
                                placeholder="Кол-во"
                                required
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>
                        <button
                            type="button"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-red-300 text-red-600 transition hover:bg-red-50"
                            @click="removeVariant(index)"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Изображения -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <h2 class="mb-4 text-xl font-bold text-gray-900">Изображения</h2>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Главное изображение <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Превью текущего изображения -->
                    <div v-if="imagePreview || form.image" class="mb-3">
                        <div class="relative inline-block h-40 w-40 overflow-hidden rounded-xl border-2 border-gray-200">
                            <img 
                                :src="imagePreview || form.image" 
                                alt="Главное изображение" 
                                class="h-full w-full object-cover" 
                            />
                            <button
                                type="button"
                                class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white shadow-lg hover:bg-red-600"
                                @click="removeMainImage"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Загрузка файла -->
                    <div class="flex items-center gap-3">
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-4 text-sm font-semibold text-gray-700 transition hover:border-blue-500 hover:bg-blue-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Выбрать файл
                            <input
                                type="file"
                                accept="image/*"
                                class="hidden"
                                :required="!isEditMode && !form.image"
                                @change="handleImageUpload"
                            />
                        </label>
                        <p class="text-xs text-gray-500">JPG, PNG, GIF до 5MB</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Дополнительные изображения
                    </label>
                    <div class="space-y-3">
                        <div
                            v-for="(img, index) in form.additional_images"
                            :key="index"
                            class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 p-3"
                        >
                            <!-- Превью -->
                            <div v-if="additionalImagePreviews[index] || img" class="relative h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200">
                                <img 
                                    :src="additionalImagePreviews[index] || img" 
                                    alt="Доп. изображение" 
                                    class="h-full w-full object-cover" 
                                />
                            </div>
                            
                            <!-- Загрузка файла -->
                            <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ additionalImagePreviews[index] || img ? 'Изменить' : 'Выбрать файл' }}
                                <input
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="(e) => handleAdditionalImageUpload(e, index)"
                                />
                            </label>
                            
                            <!-- Удалить -->
                            <button
                                type="button"
                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-red-300 text-red-600 transition hover:bg-red-50"
                                @click="removeAdditionalImage(index)"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                        <button
                            type="button"
                            class="w-full rounded-xl border-2 border-dashed border-gray-300 py-3 text-sm font-semibold text-gray-600 transition hover:border-gray-400 hover:bg-gray-50"
                            @click="addAdditionalImage"
                        >
                            + Добавить изображение
                        </button>
                    </div>
                </div>
            </div>

            <!-- Характеристики -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Характеристики</h2>
                    <button
                        type="button"
                        class="rounded-xl bg-blue-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600"
                        @click="addAttribute"
                    >
                        + Добавить характеристику
                    </button>
                </div>

                <div v-if="form.attributes.length === 0" class="rounded-xl border-2 border-dashed border-gray-300 p-8 text-center">
                    <p class="text-gray-500">Нет характеристик</p>
                </div>

                <div v-else class="space-y-3">
                    <div
                        v-for="(attr, index) in form.attributes"
                        :key="index"
                        class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4"
                    >
                        <input
                            v-model="attr.key"
                            type="text"
                            placeholder="Название (например: Материал)"
                            required
                            class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                        <input
                            v-model="attr.value"
                            type="text"
                            placeholder="Значение (например: Кожа)"
                            required
                            class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                        <button
                            type="button"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-red-300 text-red-600 transition hover:bg-red-50"
                            @click="removeAttribute(index)"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="flex items-center justify-between rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <router-link
                    to="/admin/products"
                    class="rounded-xl border border-gray-300 px-6 py-3 text-base font-semibold text-gray-700 transition hover:bg-gray-50"
                >
                    Отмена
                </router-link>
                
                <button
                    type="submit"
                    class="rounded-xl bg-blue-500 px-8 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-blue-600 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="loading"
                >
                    {{ loading ? 'Сохранение...' : (isEditMode ? 'Сохранить изменения' : 'Создать товар') }}
                </button>
            </div>

            <!-- Ошибки -->
            <div v-if="error" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600">
                {{ error }}
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';
import { useToast } from '../../composables/useToast.js';

const router = useRouter();
const route = useRoute();

const loading = ref(false);
const error = ref('');
const categories = ref([]);
const brands = ref([]);
const { success: showSuccess, error: showError } = useToast();

const form = ref({
    name: '',
    description: '',
    category_id: '',
    brand_id: '',
    gender: '',
    price: 0,
    discount_type: 'none',
    discount_value: 0,
    image: '',
    imageFile: null,
    additional_images: [],
    additionalImageFiles: [],
    variants: [],
    attributes: [],
});

const imagePreview = ref('');
const additionalImagePreviews = ref([]);

const isEditMode = computed(() => !!route.params.id);

const loadCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        categories.value = response.data.data || response.data;
    } catch (err) {
        console.error('Failed to load categories', err);
    }
};

const loadBrands = async () => {
    try {
        const response = await axios.get('/api/brands');
        brands.value = response.data.data || response.data;
    } catch (err) {
        console.error('Failed to load brands', err);
    }
};

const loadProduct = async () => {
    if (!isEditMode.value) {
        console.error('Not in edit mode, skipping product load');
        return;
    }
    
    loading.value = true;
    try {
        const token = localStorage.getItem('admin_token');
        const response = await axios.get(`/api/products/${route.params.id}`, {
            headers: { Authorization: `Bearer ${token}` },
        });
        const product = response.data.data || response.data;
        
        form.value = {
            name: product.name,
            description: product.description || '',
            category_id: product.category_id,
            brand_id: product.brand_id || '',
            gender: product.gender || '',
            price: parseFloat(product.price),
            discount_type: product.discount_type || 'none',
            discount_value: parseFloat(product.discount_value) || 0,
            image: product.image,
            imageFile: null,
            additional_images: product.images || product.additional_images || [],
            additionalImageFiles: [],
            variants: product.variants || [],
            attributes: product.attributes || [],
        };
        
        // Инициализируем массивы для дополнительных изображений
        const additionalImages = product.images || product.additional_images || [];
        additionalImagePreviews.value = additionalImages.map(() => '');
        
        // Инициализируем массив файлов для дополнительных изображений
        form.value.additionalImageFiles = additionalImages.map(() => null);
    } catch (err) {
        error.value = 'Не удалось загрузить товар';
        console.error('Failed to load product', err);
    } finally {
        loading.value = false;
    }
};

const addVariant = () => {
    form.value.variants.push({ size: '', quantity: 0 });
};

const removeVariant = (index) => {
    form.value.variants.splice(index, 1);
};

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.value.imageFile = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeMainImage = () => {
    form.value.image = '';
    form.value.imageFile = null;
    imagePreview.value = '';
};

const handleAdditionalImageUpload = (event, index) => {
    const file = event.target.files[0];
    if (file) {
        if (!form.value.additionalImageFiles[index]) {
            form.value.additionalImageFiles[index] = null;
        }
        form.value.additionalImageFiles[index] = file;
        
        const reader = new FileReader();
        reader.onload = (e) => {
            if (!additionalImagePreviews.value[index]) {
                additionalImagePreviews.value[index] = '';
            }
            additionalImagePreviews.value[index] = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const addAdditionalImage = () => {
    form.value.additional_images.push('');
    form.value.additionalImageFiles.push(null);
    additionalImagePreviews.value.push('');
};

const removeAdditionalImage = (index) => {
    form.value.additional_images.splice(index, 1);
    form.value.additionalImageFiles.splice(index, 1);
    additionalImagePreviews.value.splice(index, 1);
};

const addAttribute = () => {
    form.value.attributes.push({ key: '', value: '' });
};

const removeAttribute = (index) => {
    form.value.attributes.splice(index, 1);
};

const handleSubmit = async () => {
    error.value = '';
    loading.value = true;

    try {
        const token = localStorage.getItem('admin_token');
        const formData = new FormData();
        
        // Основные поля
        formData.append('name', form.value.name);
        formData.append('description', form.value.description);
        formData.append('category_id', form.value.category_id);
        formData.append('price', form.value.price);
        formData.append('discount_type', form.value.discount_type);
        formData.append('discount_value', form.value.discount_value || 0);
        
        if (form.value.brand_id) {
            formData.append('brand_id', form.value.brand_id);
        }
        
        if (form.value.gender) {
            formData.append('gender', form.value.gender);
        }
        
        // Главное изображение
        if (form.value.imageFile) {
            formData.append('image', form.value.imageFile);
        } else if (form.value.image) {
            formData.append('existing_image', form.value.image);
        }
        
        // Дополнительные изображения
        const existingImages = [];
        form.value.additionalImageFiles.forEach((file, index) => {
            if (file) {
                formData.append(`additional_images[]`, file);
            } else if (form.value.additional_images[index]) {
                existingImages.push(form.value.additional_images[index]);
            }
        });
        
        if (existingImages.length > 0) {
            formData.append('existing_additional_images', JSON.stringify(existingImages));
        }
        
        // Варианты - отправляем каждый элемент отдельно
        if (form.value.variants && form.value.variants.length > 0) {
            form.value.variants.forEach((variant, index) => {
                formData.append(`variants[${index}][size]`, variant.size);
                formData.append(`variants[${index}][quantity]`, variant.quantity);
                if (variant.id) {
                    formData.append(`variants[${index}][id]`, variant.id);
                }
            });
        }
        
        // Характеристики - отправляем каждый элемент отдельно
        const validAttributes = form.value.attributes.filter(attr => attr.key && attr.value);
        if (validAttributes.length > 0) {
            validAttributes.forEach((attr, index) => {
                formData.append(`attributes[${index}][key]`, attr.key);
                formData.append(`attributes[${index}][value]`, attr.value);
            });
        }

        if (isEditMode.value) {
            await axios.post(`/api/products/${route.params.id}`, formData, {
                headers: { 
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });
        } else {
            await axios.post('/api/products', formData, {
                headers: { 
                    Authorization: `Bearer ${token}`,
                    'Content-Type': 'multipart/form-data',
                },
            });
        }

        showSuccess(isEditMode.value ? 'Товар успешно обновлен!' : 'Товар успешно создан!');
        router.push('/admin/products');
    } catch (err) {
        const errorMsg = err.response?.data?.message || 'Ошибка при сохранении товара';
        error.value = errorMsg;
        showError(errorMsg);
        console.error('Failed to save product', err);
        console.error('Error details:', err.response?.data);
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    await loadCategories();
    await loadBrands();
    await loadProduct();
});
</script>
