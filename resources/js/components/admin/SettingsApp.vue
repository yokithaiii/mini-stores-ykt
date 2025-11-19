<template>
    <div>
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Настройки магазина</h1>
            <p class="mt-2 text-sm text-gray-600">Управление контактами и информацией о магазине</p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Общая информация -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <h2 class="mb-4 text-xl font-bold text-gray-900">Общая информация</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Название магазина</label>
                        <input
                            v-model="settings.store_name"
                            type="text"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Описание</label>
                        <textarea
                            v-model="settings.store_description"
                            rows="3"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Контактная информация -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <h2 class="mb-4 text-xl font-bold text-gray-900">Контактная информация</h2>
                
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Телефон</label>
                        <input
                            ref="phoneInput"
                            v-model="settings.store_phone"
                            type="tel"
                            placeholder="+7 (999) 123-45-67"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            v-model="settings.store_email"
                            type="email"
                            placeholder="info@example.com"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Адрес</label>
                        <textarea
                            v-model="settings.store_address"
                            rows="2"
                            placeholder="г. Москва, ул. Примерная, д. 1"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        ></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Часы работы</label>
                        <textarea
                            v-model="settings.store_working_hours"
                            rows="2"
                            placeholder="Пн-Пт: 9:00-20:00, Сб-Вс: 10:00-18:00"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Социальные сети -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <h2 class="mb-4 text-xl font-bold text-gray-900">Социальные сети</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Instagram</label>
                        <input
                            v-model="settings.social_instagram"
                            type="text"
                            placeholder="https://instagram.com/your_shop"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telegram</label>
                        <input
                            v-model="settings.social_telegram"
                            type="text"
                            placeholder="https://t.me/your_shop"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
                        <input
                            ref="whatsappInput"
                            v-model="settings.social_whatsapp"
                            type="tel"
                            placeholder="+7 (999) 123-45-67"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />
                    </div>
                </div>
            </div>

            <!-- Цветовая схема -->
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <h2 class="mb-4 text-xl font-bold text-gray-900">Цветовая схема</h2>
                <p class="mb-4 text-sm text-gray-600">Настройте цвета вашего магазина</p>
                
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Основной цвет</label>
                        <div class="flex items-center gap-3">
                            <input
                                v-model="settings.theme_primary_color"
                                type="color"
                                class="h-12 w-20 cursor-pointer rounded-lg border border-gray-300"
                            />
                            <input
                                v-model="settings.theme_primary_color"
                                type="text"
                                placeholder="#3b82f6"
                                class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Используется для кнопок и ссылок</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Акцентный цвет</label>
                        <div class="flex items-center gap-3">
                            <input
                                v-model="settings.theme_accent_color"
                                type="color"
                                class="h-12 w-20 cursor-pointer rounded-lg border border-gray-300"
                            />
                            <input
                                v-model="settings.theme_accent_color"
                                type="text"
                                placeholder="#22c55e"
                                class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Используется для цен и важных элементов</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Цвет успеха</label>
                        <div class="flex items-center gap-3">
                            <input
                                v-model="settings.theme_success_color"
                                type="color"
                                class="h-12 w-20 cursor-pointer rounded-lg border border-gray-300"
                            />
                            <input
                                v-model="settings.theme_success_color"
                                type="text"
                                placeholder="#22c55e"
                                class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Для успешных уведомлений</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Цвет предупреждения</label>
                        <div class="flex items-center gap-3">
                            <input
                                v-model="settings.theme_warning_color"
                                type="color"
                                class="h-12 w-20 cursor-pointer rounded-lg border border-gray-300"
                            />
                            <input
                                v-model="settings.theme_warning_color"
                                type="text"
                                placeholder="#f59e0b"
                                class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Для предупреждений</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Цвет ошибки</label>
                        <div class="flex items-center gap-3">
                            <input
                                v-model="settings.theme_error_color"
                                type="color"
                                class="h-12 w-20 cursor-pointer rounded-lg border border-gray-300"
                            />
                            <input
                                v-model="settings.theme_error_color"
                                type="text"
                                placeholder="#ef4444"
                                class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            />
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Для ошибок и удаления</p>
                    </div>
                </div>

                <!-- Превью цветов -->
                <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="mb-3 text-sm font-semibold text-gray-700">Превью:</p>
                    <div class="flex flex-wrap gap-3">
                        <button
                            type="button"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-white"
                            :style="{ backgroundColor: settings.theme_primary_color || '#3b82f6' }"
                        >
                            Основной
                        </button>
                        <button
                            type="button"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-white"
                            :style="{ backgroundColor: settings.theme_accent_color || '#22c55e' }"
                        >
                            Акцентный
                        </button>
                        <button
                            type="button"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-white"
                            :style="{ backgroundColor: settings.theme_success_color || '#22c55e' }"
                        >
                            Успех
                        </button>
                        <button
                            type="button"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-white"
                            :style="{ backgroundColor: settings.theme_warning_color || '#f59e0b' }"
                        >
                            Предупреждение
                        </button>
                        <button
                            type="button"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-white"
                            :style="{ backgroundColor: settings.theme_error_color || '#ef4444' }"
                        >
                            Ошибка
                        </button>
                    </div>
                </div>
            </div>

            <!-- Кнопки действий -->
            <div class="flex items-center justify-end gap-3 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                <button
                    type="button"
                    class="rounded-xl border border-gray-300 px-6 py-3 text-base font-semibold text-gray-700 transition hover:bg-gray-50"
                    @click="loadSettings"
                >
                    Сбросить
                </button>
                
                <button
                    type="submit"
                    class="rounded-xl bg-blue-500 px-8 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-blue-600 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="loading"
                >
                    {{ loading ? 'Сохранение...' : 'Сохранить изменения' }}
                </button>
            </div>

            <!-- Сообщения -->
            <div v-if="success" class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-600">
                {{ success }}
            </div>
            <div v-if="error" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-600">
                {{ error }}
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { usePhoneMask } from '../../composables/usePhoneMask.js';
import { useToast } from '../../composables/useToast.js';
import { useTheme } from '../../composables/useTheme.js';

const loading = ref(false);
const success = ref('');
const error = ref('');
const phoneInput = ref(null);
const whatsappInput = ref(null);
const { success: showSuccess, error: showError } = useToast();
const { updateTheme } = useTheme();

const settings = ref({
    store_name: '',
    store_description: '',
    store_phone: '',
    store_email: '',
    store_address: '',
    store_working_hours: '',
    social_instagram: '',
    social_telegram: '',
    social_whatsapp: '',
    theme_primary_color: '#3b82f6',
    theme_accent_color: '#22c55e',
    theme_success_color: '#22c55e',
    theme_warning_color: '#f59e0b',
    theme_error_color: '#ef4444',
});

const loadSettings = async () => {
    try {
        const token = localStorage.getItem('admin_token');
        const response = await axios.get('/api/admin/settings', {
            headers: { Authorization: `Bearer ${token}` },
        });
        
        // Преобразуем сгруппированные данные в плоский объект
        const data = response.data;
        Object.keys(data).forEach(group => {
            data[group].forEach(item => {
                if (settings.value.hasOwnProperty(item.key)) {
                    settings.value[item.key] = item.value || '';
                }
            });
        });
        
        console.log('Settings loaded:', settings.value);
    } catch (err) {
        error.value = 'Не удалось загрузить настройки';
        console.error('Failed to load settings', err);
    }
};

const handleSubmit = async () => {
    success.value = '';
    error.value = '';
    loading.value = true;

    try {
        const token = localStorage.getItem('admin_token');
        
        // Преобразуем объект настроек в массив
        const settingsArray = Object.keys(settings.value).map(key => ({
            key,
            value: settings.value[key] || '',
        }));

        await axios.post('/api/admin/settings', {
            settings: settingsArray,
        }, {
            headers: { Authorization: `Bearer ${token}` },
        });

        // Обновляем тему в реальном времени
        updateTheme({
            primary_color: settings.value.theme_primary_color,
            accent_color: settings.value.theme_accent_color,
            success_color: settings.value.theme_success_color,
            warning_color: settings.value.theme_warning_color,
            error_color: settings.value.theme_error_color,
        });
        
        showSuccess('Настройки успешно сохранены!');
        success.value = 'Настройки успешно сохранены!';
        setTimeout(() => {
            success.value = '';
        }, 3000);
    } catch (err) {
        const errorMsg = err.response?.data?.message || 'Ошибка при сохранении настроек';
        error.value = errorMsg;
        showError(errorMsg);
        console.error('Failed to save settings', err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadSettings();
    // Инициализируем маски телефона
    usePhoneMask(phoneInput);
    usePhoneMask(whatsappInput);
});
</script>
