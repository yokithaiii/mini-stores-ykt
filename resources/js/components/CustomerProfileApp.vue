<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-green-50">
        <!-- Шапка -->
        <AppHeader
            :is-customer-logged-in="true"
            :customer-name="customer.name"
            :cart-items-count="cartItemsCount"
            :favorites-count="favoritesCount"
            @toggle-cart="showCart = true"
            @show-auth="() => {}"
        />

        <main class="mx-auto max-w-7xl px-4 py-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Мой профиль</h1>
                <button
                    class="rounded-xl border border-red-300 px-5 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                    @click="logout"
                >
                    Выйти
                </button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Профиль -->
                <div class="lg:col-span-1">
                    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Личные данные</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Телефон</label>
                                <input
                                    :value="customer.phone"
                                    disabled
                                    class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-base text-gray-900"
                                />
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-700">Имя</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Ваше имя"
                                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                />
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-700">Email</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="email@example.com"
                                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                />
                            </div>
                            
                            <button
                                class="w-full rounded-xl py-3 text-base font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                                :style="{ backgroundColor: saving ? colors.neutral[400] : colors.primary[500] }"
                                :disabled="saving"
                                @click="updateProfile"
                            >
                                {{ saving ? 'Сохраняем...' : 'Сохранить' }}
                            </button>
                        </div>
                        
                        <p v-if="successMessage" class="mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-600">
                            {{ successMessage }}
                        </p>
                    </div>
                </div>

                <!-- Заказы и избранное -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Табы -->
                    <div class="flex gap-2 border-b border-gray-200">
                        <button
                            class="px-6 py-3 text-sm font-semibold transition"
                            :class="activeTab === 'orders' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                            @click="activeTab = 'orders'"
                        >
                            Мои заказы
                        </button>
                        <button
                            class="px-6 py-3 text-sm font-semibold transition"
                            :class="activeTab === 'favorites' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                            @click="activeTab = 'favorites'"
                        >
                            Избранное
                        </button>
                    </div>

                    <!-- Заказы -->
                    <div v-if="activeTab === 'orders'" class="space-y-4">
                        <div v-if="loadingOrders" class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-900/5">
                            <p class="text-gray-500">Загружаем заказы...</p>
                        </div>
                        
                        <div v-else-if="!orders.length" class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-900/5">
                            <p class="text-gray-500">У вас пока нет заказов</p>
                        </div>
                        
                        <div
                            v-for="order in orders"
                            :key="order.id"
                            class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5"
                        >
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <p class="text-sm text-gray-500">Заказ #{{ order.id }}</p>
                                    <p class="text-xs text-gray-400">{{ formatDate(order.created_at) }}</p>
                                </div>
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="getStatusClass(order.status)"
                                >
                                    {{ getStatusLabel(order.status) }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <div
                                    v-for="item in order.items"
                                    :key="item.id"
                                    class="flex items-center gap-3 text-sm"
                                >
                                    <img
                                        v-if="item.product?.image"
                                        :src="item.product.image"
                                        :alt="item.product.name"
                                        class="h-12 w-12 rounded-lg object-cover"
                                    />
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-900">{{ item.product?.name }}</p>
                                        <p class="text-gray-500">
                                            {{ item.size ? `Размер: ${item.size} • ` : '' }}{{ item.quantity }} шт.
                                        </p>
                                    </div>
                                    <p class="font-semibold text-gray-900">{{ formatPrice(item.price * item.quantity) }}</p>
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
                                <p class="text-sm text-gray-600">Итого:</p>
                                <p class="text-xl font-bold" :style="{ color: colors.accent[600] }">
                                    {{ formatPrice(order.total_price) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Избранное -->
                    <div v-if="activeTab === 'favorites'" class="space-y-4">
                        <div v-if="loadingFavorites" class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-900/5">
                            <p class="text-gray-500">Загружаем избранное...</p>
                        </div>
                        
                        <div v-else-if="!favorites.length" class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-900/5">
                            <p class="text-gray-500">У вас пока нет избранных товаров</p>
                        </div>
                        
                        <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <article
                                v-for="product in favorites"
                                :key="product.id"
                                class="flex flex-col group rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-900/5 transition-all hover:shadow-md"
                            >
                                <router-link :to="`/product/${product.id}`" class="block">
                                    <div class="mb-4 aspect-square overflow-hidden rounded-xl bg-gray-100 relative">
                                        <img 
                                            v-if="product.image" 
                                            :src="product.image" 
                                            :alt="product.name"
                                            class="h-full w-full object-cover transition-transform group-hover:scale-105"
                                        />
                                        <button
                                            class="absolute right-2 top-2 flex h-9 w-9 items-center justify-center rounded-full bg-white shadow-md transition hover:scale-110 z-10 text-red-500"
                                            @click.prevent="removeFromFavorites(product.id)"
                                        >
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <h3 class="text-lg font-bold text-gray-900">{{ product.name }}</h3>
                                </router-link>
                                
                                <p class="text-xs text-gray-500 mt-auto">
                                    {{ getCategoryName(product.category_id) }}
                                </p>
                                
                                <div class="mt-3">
                                    <div class="flex items-center gap-2">
                                        <p 
                                            v-if="product.discount_type !== 'none' && product.discount_value"
                                            class="text-sm text-gray-400 line-through"
                                        >
                                            {{ formatPrice(product.price) }}
                                        </p>
                                        <p class="text-xl font-bold" :style="{ color: colors.accent[600] }">
                                            {{ formatPrice(product.discounted_price || product.price) }}
                                        </p>
                                        <span 
                                            v-if="product.discount_type !== 'none' && product.discount_value"
                                            class="rounded-full px-2 py-0.5 text-xs font-bold text-white"
                                            :style="{ backgroundColor: colors.error }"
                                        >
                                            -{{ formatDiscount(product) }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600">
                                        В наличии: {{ getTotalQuantity(product) }}
                                    </p>
                                </div>
                                
                                <router-link
                                    :to="`/product/${product.id}`"
                                    class="mt-4 block w-full rounded-xl py-2.5 text-center text-sm font-semibold text-white shadow-sm transition-all hover:shadow-md"
                                    :style="{ backgroundColor: colors.primary[500] }"
                                >
                                    Подробнее
                                </router-link>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Корзина -->
        <CartSidebar
            :show="showCart"
            @close="showCart = false"
            @checkout="openCheckout"
        />

        <!-- Модалка оформления заказа -->
        <CheckoutModal
            :show="showCheckoutModal"
            :items="cart"
            :total-price="totalPrice"
            :is-logged-in="true"
            :customer-name="customer.name"
            :customer-phone="customer.phone"
            @close="showCheckoutModal = false"
            @success="onOrderSuccess"
            @show-auth="() => {}"
        />

        <!-- Футер -->
        <AppFooter />
    </div>
</template>

<script setup>
import AppFooter from './AppFooter.vue';
import axios from 'axios';
import { computed, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useTheme } from '../composables/useTheme.js';
import { useCart } from '../composables/useCart.js';
import { useFavorites } from '../composables/useFavorites.js';
import CartSidebar from './CartSidebar.vue';
import AppHeader from './AppHeader.vue';
import CheckoutModal from './CheckoutModal.vue';
import { useToast } from '../composables/useToast.js';

const router = useRouter();
const { success: showSuccess, error: showError } = useToast();
const { cart, cartItemsCount, clearCart } = useCart();
const { favoritesCount } = useFavorites();
const { theme: colors } = useTheme();

const customer = ref({});
const orders = ref([]);
const favorites = ref([]);
const categories = ref([]);
const activeTab = ref('orders');
const loadingOrders = ref(false);
const loadingFavorites = ref(false);
const saving = ref(false);
const successMessage = ref('');
const showCart = ref(false);
const showCheckoutModal = ref(false);

const form = reactive({
    name: '',
    email: '',
});

const fetchProfile = async () => {
    try {
        const phone = localStorage.getItem('customer_phone');
        const response = await axios.get('/api/customer/profile', {
            headers: {
                'X-Customer-Phone': phone,
            },
        });
        customer.value = response.data;
        form.name = response.data.name || '';
        form.email = response.data.email || '';
    } catch (error) {
        console.error(error);
        window.location.href = '/';
    }
};

const fetchOrders = async () => {
    loadingOrders.value = true;
    try {
        const phone = localStorage.getItem('customer_phone');
        const response = await axios.get('/api/orders', {
            headers: {
                'X-Customer-Phone': phone,
            },
        });
        orders.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        loadingOrders.value = false;
    }
};

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        categories.value = response.data.data || response.data;
    } catch (error) {
        console.error(error);
    }
};

const fetchFavorites = async () => {
    loadingFavorites.value = true;
    try {
        const phone = localStorage.getItem('customer_phone');
        const response = await axios.get('/api/favorites', {
            headers: {
                'X-Customer-Phone': phone,
            },
        });
        favorites.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        loadingFavorites.value = false;
    }
};

const updateProfile = async () => {
    saving.value = true;
    successMessage.value = '';
    
    try {
        const phone = localStorage.getItem('customer_phone');
        const response = await axios.post('/api/customer/profile', form, {
            headers: {
                'X-Customer-Phone': phone,
            },
        });
        customer.value = response.data.customer;
        localStorage.setItem('customer_data', JSON.stringify(response.data.customer));
        showSuccess('Профиль успешно обновлен');
        successMessage.value = 'Профиль обновлен';
        
        setTimeout(() => {
            successMessage.value = '';
        }, 3000);
    } catch (error) {
        console.error(error);
    } finally {
        saving.value = false;
    }
};

const removeFromFavorites = async (productId) => {
    try {
        const phone = localStorage.getItem('customer_phone');
        await axios.delete(`/api/favorites/${productId}`, {
            headers: {
                'X-Customer-Phone': phone,
            },
        });
        favorites.value = favorites.value.filter(p => p.id !== productId);
        showSuccess('Товар удален из избранного');
    } catch (error) {
        console.error(error);
        showError('Не удалось удалить из избранного');
    }
};

const logout = () => {
    localStorage.removeItem('customer_phone');
    localStorage.removeItem('customer_data');
    clearFavorites();
    clearCart();
    router.push('/');
};

const openCheckout = () => {
    showCart.value = false;
    showCheckoutModal.value = true;
};

const onOrderSuccess = async () => {
    clearCart();
    showCheckoutModal.value = false;
    await fetchOrders(); // Обновляем список заказов
    activeTab.value = 'orders'; // Переключаемся на вкладку заказов
};

const totalPrice = computed(() => {
    return cart.value.reduce((sum, item) => {
        const price = item.discounted_price || item.price;
        return sum + (price * item.cartQuantity);
    }, 0);
});



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

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Ожидает',
        confirmed: 'Подтвержден',
        cancelled: 'Отменен',
    };
    return labels[status] || status;
};

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-700',
        confirmed: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-700',
    };
    return classes[status] || 'bg-gray-100 text-gray-700';
};

const getCategoryName = (categoryId) => {
    const category = categories.value.find(c => c.id === categoryId);
    return category?.name || '—';
};

const getTotalQuantity = (product) => {
    if (!product) return 0;
    
    if (product.variants && product.variants.length > 0) {
        return product.variants.reduce((sum, v) => sum + v.quantity, 0);
    }
    
    return product.quantity || 0;
};

const formatDiscount = (product) => {
    if (!product || product.discount_type === 'none' || !product.discount_value) {
        return '';
    }

    if (product.discount_type === 'percent') {
        return `${product.discount_value}%`;
    }

    return formatPrice(product.discount_value);
};

onMounted(async () => {
    await fetchProfile();
    await fetchCategories();
    await fetchOrders();
    await fetchFavorites();
});
</script>
