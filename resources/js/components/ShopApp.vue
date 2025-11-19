<template>
    <div class="min-h-screen">
        <!-- Шапка -->
        <AppHeader
            :is-customer-logged-in="isCustomerLoggedIn"
            :customer-name="customerName"
            :cart-items-count="cartItemsCount"
            :favorites-count="favoritesCount"
            @toggle-cart="toggleCart"
            @show-auth="showAuthModal = true"
        />

        <main class="mx-auto max-w-7xl px-4 py-8">
            <!-- Кнопка фильтров для мобильных -->
            <div class="mb-4 lg:hidden">
                <button
                    class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    @click="showMobileFilters = true"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Фильтры
                    <span v-if="hasActiveFilters" class="rounded-full bg-blue-500 px-2 py-0.5 text-xs text-white">
                        {{ activeFiltersCount }}
                    </span>
                </button>
            </div>

            <div class="flex gap-6">
                <!-- Боковая панель с фильтрами (десктоп) -->
                <aside class="hidden w-64 flex-shrink-0 lg:block">
                    <div class="sticky top-24 space-y-6">
                        <!-- Заголовок фильтров -->
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Фильтры</h3>
                            <button
                                v-if="hasActiveFilters"
                                class="text-sm font-semibold text-blue-600 hover:text-blue-700"
                                @click="resetFilters"
                            >
                                Сбросить
                            </button>
                        </div>

                        <!-- Фильтры -->
                        <div class="space-y-6 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-900/5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Сортировка</label>
                                <select
                                    v-model="sortBy"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                >
                                    <option value="newest">Сначала новые</option>
                                    <option value="price_asc">Цена: дешевле</option>
                                    <option value="price_desc">Цена: дороже</option>
                                </select>
                            </div>

                            <!-- Категория -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Категория</label>
                                <select
                                    v-model="filters.category_id"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    @change="applyFilters"
                                >
                                    <option value="">Все категории</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Бренд -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Бренд</label>
                                <select
                                    v-model="filters.brand_id"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    @change="applyFilters"
                                >
                                    <option value="">Все бренды</option>
                                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                        {{ brand.name }}
                                    </option>
                                </select>
                            </div>
                            
                            <!-- Пол -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Пол</label>
                                <select
                                    v-model="filters.gender"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    @change="applyFilters"
                                >
                                    <option value="">Все</option>
                                    <option value="male">Мужской</option>
                                    <option value="female">Женский</option>
                                    <option value="unisex">Унисекс</option>
                                </select>
                            </div>
                            
                            <!-- Цена -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">Цена</label>
                                <div class="space-y-2">
                                    <input
                                        v-model.number="filters.price_min"
                                        type="number"
                                        min="0"
                                        placeholder="От"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                        @input="applyFilters"
                                    />
                                    <input
                                        v-model.number="filters.price_max"
                                        type="number"
                                        min="0"
                                        placeholder="До"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                        @input="applyFilters"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Активные фильтры -->
                        <div v-if="hasActiveFilters" class="space-y-2">
                            <p class="text-sm font-semibold text-gray-900">Активные фильтры:</p>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-if="filters.category_id"
                                    class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700"
                                >
                                    {{ categoryMap[filters.category_id] }}
                                    <button @click="filters.category_id = ''; applyFilters()">×</button>
                                </span>
                                <span
                                    v-if="filters.brand_id"
                                    class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700"
                                >
                                    {{ brandMap[filters.brand_id] }}
                                    <button @click="filters.brand_id = ''; applyFilters()">×</button>
                                </span>
                                <span
                                    v-if="filters.gender"
                                    class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700"
                                >
                                    {{ getGenderLabel(filters.gender) }}
                                    <button @click="filters.gender = ''; applyFilters()">×</button>
                                </span>
                                <span
                                    v-if="filters.price_min || filters.price_max"
                                    class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700"
                                >
                                    {{ filters.price_min || 0 }}₽ - {{ filters.price_max || '∞' }}₽
                                    <button @click="filters.price_min = null; filters.price_max = null; applyFilters()">×</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Основной контент -->
                <div class="flex-1">
                    <!-- Товары -->
                    <!-- Скелетоны при загрузке -->
                    <div v-if="loading" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="i in 8"
                            :key="i"
                            class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-900/5"
                        >
                            <div class="mb-4 aspect-square animate-pulse rounded-xl bg-gray-200"></div>
                            <div class="h-5 w-3/4 animate-pulse rounded bg-gray-200"></div>
                            <div class="mt-2 h-3 w-1/2 animate-pulse rounded bg-gray-200"></div>
                            <div class="mt-3 h-6 w-1/3 animate-pulse rounded bg-gray-200"></div>
                            <div class="mt-4 h-10 w-full animate-pulse rounded-xl bg-gray-200"></div>
                        </div>
                    </div>

                    <div v-else-if="!sortedAndFilteredProducts.length" class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-900/5">
                        <p class="text-gray-500">Товары не найдены</p>
                    </div>

                    <div v-else>
                        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="product in paginatedProducts"
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
                            <!-- Кнопка избранного -->
                            <button
                                v-if="isCustomerLoggedIn"
                                class="absolute right-2 top-2 flex h-9 w-9 items-center justify-center rounded-full bg-white shadow-md transition hover:scale-110 z-10"
                                :class="isInFavorites(product.id) ? 'text-red-500' : 'text-gray-400 hover:text-red-500'"
                                @click.prevent="toggleFavorite(product.id)"
                            >
                                <svg class="h-5 w-5" :fill="isInFavorites(product.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900">{{ product.name }}</h3>
                    </router-link>
                    
                    <p class="mt-1 text-xs text-gray-500">
                        {{ categoryMap[product.category_id] ?? '—' }}
                    </p>
                    
                    <div class="mt-3 mb-4">
                        <div class="flex items-center gap-2">
                            <p 
                                v-if="product.discount_type !== 'none' && product.discount_value"
                                class="text-sm text-gray-400 line-through"
                            >
                                {{ formatPrice(product.price) }}
                            </p>
                            <p class="text-xl font-bold" :style="{ color: colors.accent[600] }">
                                {{ formatPrice(getDiscountedPrice(product)) }}
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
                    
                    <!-- Кнопки добавления в корзину -->
                    <div class="mt-auto">
                        <!-- Если товар НЕ в корзине - показываем кнопку "В корзину" -->
                        <button
                            v-if="!isInCart(product.id)"
                            class="w-full rounded-xl py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                            :style="{ backgroundColor: getTotalQuantity(product) === 0 ? colors.neutral[400] : colors.primary[500] }"
                            :disabled="getTotalQuantity(product) === 0"
                            @click="handleAddToCart(product)"
                        >
                            {{ getTotalQuantity(product) === 0 ? 'Нет в наличии' : 'В корзину' }}
                        </button>
                        
                        <!-- Если товар В корзине - показываем кнопки +/- -->
                        <div v-else class="flex items-center justify-center gap-3">
                            <button
                                class="flex h-10 w-10 items-center justify-center rounded-lg border-2 border-gray-300 text-xl font-bold text-gray-700 transition hover:bg-gray-50"
                                @click="decreaseQuantityFromCard(product.id)"
                            >
                                −
                            </button>
                            <span class="min-w-[3rem] text-center text-lg font-bold text-gray-900">
                                {{ getCartQuantity(product.id) }}
                            </span>
                            <button
                                class="flex h-10 w-10 items-center justify-center rounded-lg border-2 transition disabled:cursor-not-allowed disabled:opacity-50"
                                :class="canIncreaseInCart(product.id) ? 'border-blue-500 text-blue-600 hover:bg-blue-50' : 'border-gray-300 text-gray-400'"
                                :disabled="!canIncreaseInCart(product.id)"
                                @click="increaseQuantityFromCard(product.id)"
                            >
                                +
                            </button>
                        </div>
                    </div>
                </article>
                        </div>

                        <!-- Пагинация -->
                        <div v-if="totalPages > 1" class="mt-8 flex justify-center">
                <div class="flex items-center gap-2">
                    <button
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="currentPage === 1"
                        @click="currentPage--"
                    >
                        ← Назад
                    </button>
                    
                    <div class="flex items-center gap-1">
                        <button
                            v-for="page in visiblePages"
                            :key="page"
                            class="h-10 w-10 rounded-lg text-sm font-semibold transition"
                            :class="page === currentPage ? 'bg-blue-500 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-50'"
                            @click="currentPage = page"
                        >
                            {{ page }}
                        </button>
                    </div>
                    
                    <button
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="currentPage === totalPages"
                        @click="currentPage++"
                    >
                        Вперед →
                    </button>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Модалка просмотра товара -->
        <transition name="fade">
            <div
                v-if="productModal.open"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4 py-6 backdrop-blur-sm"
                @click.self="closeProductModal"
            >
                <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-xl">
                    <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white p-6">
                        <h2 class="text-2xl font-bold text-gray-900">Информация о товаре</h2>
                        <button
                            class="text-gray-600 hover:text-gray-900"
                            @click="closeProductModal"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div v-if="productModal.product" class="p-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <!-- Галерея изображений -->
                            <div class="space-y-3">
                                <!-- Главное изображение -->
                                <div class="aspect-square overflow-hidden rounded-xl bg-gray-100">
                                    <img 
                                        :src="getCurrentImage(productModal.product)" 
                                        :alt="productModal.product.name"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                
                                <!-- Thumbnails -->
                                <div v-if="getAllImages(productModal.product).length > 1" class="flex gap-2 overflow-x-auto">
                                    <button
                                        v-for="(img, index) in getAllImages(productModal.product)"
                                        :key="index"
                                        class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg border-2 transition"
                                        :class="currentImageIndex === index ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-gray-200 hover:border-gray-300'"
                                        @click="currentImageIndex = index"
                                    >
                                        <img :src="img" :alt="`${productModal.product.name} ${index + 1}`" class="h-full w-full object-cover" />
                                    </button>
                                </div>
                            </div>

                            <!-- Информация -->
                            <div class="space-y-4 flex flex-col">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ productModal.product.name }}</h3>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-gray-600">
                                        <span v-if="productModal.product.brand">{{ productModal.product.brand.name }}</span>
                                        <span v-if="productModal.product.brand && categoryMap[productModal.product.category_id]">•</span>
                                        <span>{{ categoryMap[productModal.product.category_id] ?? '—' }}</span>
                                        <span v-if="productModal.product.gender">•</span>
                                        <span v-if="productModal.product.gender">{{ getGenderLabel(productModal.product.gender) }}</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center gap-2">
                                        <p 
                                            v-if="productModal.product.discount_type !== 'none' && productModal.product.discount_value"
                                            class="text-xl text-gray-400 line-through"
                                        >
                                            {{ formatPrice(productModal.product.price) }}
                                        </p>
                                        <p class="text-3xl font-bold" :style="{ color: colors.accent[600] }">
                                            {{ formatPrice(getDiscountedPrice(productModal.product)) }}
                                        </p>
                                    </div>
                                    <div v-if="productModal.product.discount_type !== 'none' && productModal.product.discount_value" class="mt-1">
                                        <span class="inline-block rounded-full px-3 py-1 text-sm font-bold text-white" :style="{ backgroundColor: colors.error }">
                                            Скидка {{ formatDiscount(productModal.product) }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">
                                        В наличии: {{ getTotalQuantity(productModal.product) }} шт.
                                    </p>
                                </div>

                                <!-- Размеры - ОБЯЗАТЕЛЬНО -->
                                <div class="border-t border-gray-200 pt-4">
                                    <h4 class="font-semibold text-gray-900">
                                        Выберите размер <span class="text-red-500">*</span>
                                    </h4>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <button
                                            v-for="variant in productModal.product.variants"
                                            :key="variant.id"
                                            class="rounded-lg border px-4 py-2 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50"
                                            :class="selectedVariant?.id === variant.id ? 'border-blue-500 bg-blue-50 text-blue-700 ring-2 ring-blue-500/20' : 'border-gray-300 text-gray-700 hover:border-gray-400'"
                                            :disabled="variant.quantity === 0"
                                            @click="selectVariant(variant)"
                                        >
                                            {{ variant.size }}
                                            <span v-if="variant.quantity > 0" class="ml-1 text-xs text-gray-500">({{ variant.quantity }})</span>
                                            <span v-else class="ml-1 text-xs text-red-500">(нет)</span>
                                        </button>
                                    </div>
                                    <p v-if="!selectedVariant" class="mt-2 text-xs text-red-600">
                                        Выберите размер для добавления в корзину
                                    </p>
                                </div>

                                <!-- Характеристики -->
                                <div v-if="productModal.product.attributes && productModal.product.attributes.length > 0" class="border-t border-gray-200 pt-4">
                                    <h4 class="font-semibold text-gray-900">Характеристики</h4>
                                    <dl class="mt-2 space-y-2">
                                        <div v-for="(attr, index) in productModal.product.attributes" :key="index" class="flex justify-between text-sm">
                                            <dt class="text-gray-600">{{ attr.key }}:</dt>
                                            <dd class="font-medium text-gray-900">{{ attr.value }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <div v-if="productModal.product.description" class="border-t border-gray-200 pt-4">
                                    <h4 class="font-semibold text-gray-900">Описание</h4>
                                    <p class="mt-2 whitespace-pre-line text-gray-700">{{ productModal.product.description }}</p>
                                </div>

                                <!-- Кнопки действий -->
                                <div class="border-t border-gray-200 pt-4 mt-auto">
                                    <button
                                        class="w-full rounded-xl py-3 text-base font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                                        :style="{ backgroundColor: canAddToCart(productModal.product) ? colors.primary[500] : colors.neutral[400] }"
                                        :disabled="!canAddToCart(productModal.product)"
                                        @click="addToCartFromModal(productModal.product)"
                                    >
                                        {{ getAddToCartButtonText(productModal.product) }}
                                    </button>
                                    <p v-if="productModal.product.variants && productModal.product.variants.length > 0 && !selectedVariant" class="mt-2 text-center text-sm text-gray-500">
                                        Выберите размер
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Корзина (боковая панель) -->
        <CartSidebar
            :show="showCart"
            @close="showCart = false"
            @checkout="openCheckout"
        />

        <!-- Старая корзина (удалить после проверки) -->
        <transition name="cart" v-if="false">
            <div
                v-if="showCart"
                class="fixed inset-0 z-50 flex items-start justify-end bg-black/40 backdrop-blur-sm"
                @click.self="toggleCart"
            >
                <div class="h-full w-full max-w-md overflow-y-auto bg-white shadow-2xl">
                    <div class="sticky top-0 z-10 border-b border-gray-200 bg-white p-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-gray-900">Корзина</h2>
                            <button
                                class="text-gray-600 hover:text-gray-900"
                                @click="toggleCart"
                            >
                                Закрыть
                            </button>
                        </div>
                    </div>

                    <div v-if="!cart.length" class="p-6 text-center">
                        <p class="text-gray-500">Корзина пуста</p>
                    </div>

                    <div v-else class="p-6">
                        <ul class="space-y-4">
                            <li
                                v-for="(item, index) in cart"
                                :key="`${item.id}-${item.selectedSize || 'default'}-${index}`"
                                class="flex gap-4 rounded-xl bg-gray-50 p-4"
                            >
                                <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-lg bg-gray-200">
                                    <img 
                                        v-if="item.image" 
                                        :src="item.image" 
                                        :alt="item.name"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900">{{ item.name }}</h3>
                                    <p v-if="item.selectedSize" class="text-xs text-gray-500">Размер: {{ item.selectedSize }}</p>
                                    <div class="flex items-center gap-2">
                                        <p 
                                            v-if="item.discount_type !== 'none' && item.discount_value"
                                            class="text-xs text-gray-400 line-through"
                                        >
                                            {{ formatPrice(item.price) }}
                                        </p>
                                        <p class="text-sm font-semibold text-gray-900">{{ formatPrice(getDiscountedPrice(item)) }}</p>
                                    </div>
                                    
                                    <div class="mt-2 flex items-center gap-2">
                                        <button
                                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100"
                                            @click="decreaseQuantity(index)"
                                        >
                                            −
                                        </button>
                                        <span class="w-8 text-center font-semibold">{{ item.cartQuantity }}</span>
                                        <button
                                            class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
                                            :disabled="item.cartQuantity >= getMaxQuantityForItem(item)"
                                            @click="increaseQuantity(index)"
                                        >
                                            +
                                        </button>
                                        <button
                                            class="ml-auto text-sm font-semibold text-red-600 hover:text-red-700"
                                            @click="removeFromCart(index)"
                                        >
                                            Удалить
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <div class="mt-6 space-y-3 border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between text-lg font-bold">
                                <span>Итого:</span>
                                <span :style="{ color: colors.accent[600] }">{{ formatPrice(totalPrice) }}</span>
                            </div>
                            
                            <button
                                class="w-full rounded-xl py-3 text-base font-semibold text-white shadow-sm transition-all hover:shadow-md disabled:cursor-not-allowed disabled:opacity-60"
                                :style="{ backgroundColor: checkoutLoading ? colors.neutral[400] : colors.accent[500] }"
                                :disabled="checkoutLoading"
                                @click="checkout"
                            >
                                {{ checkoutLoading ? 'Оформляем...' : 'Оформить заказ' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Мобильные фильтры -->
        <transition name="slide">
            <div
                v-if="showMobileFilters"
                class="fixed inset-0 z-50 flex items-end justify-center bg-black/40 backdrop-blur-sm lg:hidden"
                @click.self="showMobileFilters = false"
            >
                <div class="max-h-[85vh] w-full overflow-y-auto rounded-t-3xl bg-white shadow-xl">
                    <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white p-4">
                        <h3 class="text-lg font-bold text-gray-900">Фильтры</h3>
                        <button
                            class="text-gray-600 hover:text-gray-900"
                            @click="showMobileFilters = false"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-4 space-y-6">
                        <!-- Сортировка -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Сортировка</label>
                            <select
                                v-model="sortBy"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                            >
                                <option value="newest">Сначала новые</option>
                                <option value="price_asc">Цена: дешевле</option>
                                <option value="price_desc">Цена: дороже</option>
                            </select>
                        </div>

                        <!-- Категория -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Категория</label>
                            <select
                                v-model="filters.category_id"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                @change="applyFilters"
                            >
                                <option value="">Все категории</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>
                        
                        <!-- Бренд -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Бренд</label>
                            <select
                                v-model="filters.brand_id"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                @change="applyFilters"
                            >
                                <option value="">Все бренды</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                    {{ brand.name }}
                                </option>
                            </select>
                        </div>
                        
                        <!-- Пол -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Пол</label>
                            <select
                                v-model="filters.gender"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                @change="applyFilters"
                            >
                                <option value="">Все</option>
                                <option value="male">Мужской</option>
                                <option value="female">Женский</option>
                                <option value="unisex">Унисекс</option>
                            </select>
                        </div>
                        
                        <!-- Цена -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Цена</label>
                            <div class="space-y-2">
                                <input
                                    v-model.number="filters.price_min"
                                    type="number"
                                    min="0"
                                    placeholder="От"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    @input="applyFilters"
                                />
                                <input
                                    v-model.number="filters.price_max"
                                    type="number"
                                    min="0"
                                    placeholder="До"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                    @input="applyFilters"
                                />
                            </div>
                        </div>

                        <!-- Кнопки действий -->
                        <div class="flex gap-3 border-t border-gray-200 pt-4">
                            <button
                                v-if="hasActiveFilters"
                                class="flex-1 rounded-xl border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                                @click="resetFilters"
                            >
                                Сбросить
                            </button>
                            <button
                                class="flex-1 rounded-xl bg-blue-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-600"
                                @click="showMobileFilters = false"
                            >
                                Применить
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Модалка авторизации -->
        <CustomerAuthModal
            :show="showAuthModal"
            @close="showAuthModal = false"
            @success="onAuthSuccess"
        />

        <!-- Модалка выбора размера -->
        <SizeSelectionModal
            :show="showSizeModal"
            :product="selectedProduct"
            @close="showSizeModal = false"
            @add-to-cart="addToCartWithSize"
        />

        <!-- Модалка оформления заказа -->
        <CheckoutModal
            :show="showCheckoutModal"
            :items="cart"
            :total-price="totalPrice"
            :is-logged-in="isCustomerLoggedIn"
            :customer-name="customerName"
            :customer-phone="customerPhone"
            @close="showCheckoutModal = false"
            @success="onOrderSuccess"
            @show-auth="showAuthModal = true; showCheckoutModal = false"
        />

        <!-- Футер -->
        <AppFooter />
    </div>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, reactive, ref, watch } from 'vue';
import AppFooter from './AppFooter.vue';
import { useRouter } from 'vue-router';
import { useCart } from '../composables/useCart.js';
import { useFavorites } from '../composables/useFavorites.js';
import CustomerAuthModal from './CustomerAuthModal.vue';
import AppHeader from './AppHeader.vue';
import SizeSelectionModal from './SizeSelectionModal.vue';
import CheckoutModal from './CheckoutModal.vue';
import CartSidebar from './CartSidebar.vue';
import { useToast } from '../composables/useToast.js';
import { useTheme } from '../composables/useTheme.js';

const router = useRouter();
const { cart, cartItemsCount, saveCart, clearCart } = useCart();
const { favorites, favoritesCount, isInFavorites: checkInFavorites, addToFavorites, removeFromFavorites, loadFavorites } = useFavorites();
const { theme: colors } = useTheme();

const stores = ref([]);
const categories = ref([]);
const brands = ref([]);
const products = ref([]);
const showCart = ref(false);
const loading = ref(false);
const showMobileFilters = ref(false);

const productModal = reactive({
    open: false,
    product: null,
});

const selectedVariant = ref(null);
const currentImageIndex = ref(0);

const filters = reactive({
    category_id: '',
    brand_id: '',
    gender: '',
    price_min: null,
    price_max: null,
});

// Сортировка и пагинация
const sortBy = ref('newest');
const currentPage = ref(1);
const itemsPerPage = 12;

// Авторизация клиента
const showAuthModal = ref(false);
const isCustomerLoggedIn = ref(false);
const customerName = ref('');
const customerPhone = ref('');

// Модалка выбора размера
const showSizeModal = ref(false);
const selectedProduct = ref(null);

// Модалка оформления заказа
const showCheckoutModal = ref(false);

const handleResponseData = (response) => response?.data?.data ?? response?.data ?? [];

const fetchStores = async () => {
    try {
        const response = await axios.get('/api/stores');
        stores.value = handleResponseData(response);
    } catch (error) {
        console.error(error);
    }
};

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        categories.value = handleResponseData(response);
    } catch (error) {
        console.error(error);
    }
};

const fetchBrands = async () => {
    try {
        const response = await axios.get('/api/brands');
        brands.value = handleResponseData(response);
    } catch (error) {
        console.error(error);
    }
};

const fetchProducts = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/products');
        products.value = handleResponseData(response);
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
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

const brandMap = computed(() =>
    brands.value.reduce((acc, brand) => {
        acc[brand.id] = brand.name;
        return acc;
    }, {})
);

const filteredProducts = computed(() => {
    let result = products.value;
    
    if (filters.category_id) {
        result = result.filter(p => p.category_id === filters.category_id);
    }
    
    if (filters.brand_id) {
        result = result.filter(p => p.brand_id === filters.brand_id);
    }
    
    if (filters.gender) {
        result = result.filter(p => p.gender === filters.gender);
    }
    
    if (filters.price_min !== null && filters.price_min !== '') {
        result = result.filter(p => getDiscountedPrice(p) >= filters.price_min);
    }
    
    if (filters.price_max !== null && filters.price_max !== '') {
        result = result.filter(p => getDiscountedPrice(p) <= filters.price_max);
    }
    
    return result;
});

// Сортировка товаров
const sortedAndFilteredProducts = computed(() => {
    let result = [...filteredProducts.value];
    
    if (sortBy.value === 'price_asc') {
        result.sort((a, b) => getDiscountedPrice(a) - getDiscountedPrice(b));
    } else if (sortBy.value === 'price_desc') {
        result.sort((a, b) => getDiscountedPrice(b) - getDiscountedPrice(a));
    } else if (sortBy.value === 'newest') {
        // Предполагаем, что новые товары имеют больший ID или created_at
        result.sort((a, b) => b.id - a.id);
    }
    
    return result;
});

// Пагинация
const totalPages = computed(() => {
    return Math.ceil(sortedAndFilteredProducts.value.length / itemsPerPage);
});

const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return sortedAndFilteredProducts.value.slice(start, end);
});

// Видимые страницы для пагинации
const visiblePages = computed(() => {
    const pages = [];
    const total = totalPages.value;
    const current = currentPage.value;
    
    if (total <= 7) {
        for (let i = 1; i <= total; i++) {
            pages.push(i);
        }
    } else {
        if (current <= 4) {
            for (let i = 1; i <= 5; i++) pages.push(i);
            pages.push('...');
            pages.push(total);
        } else if (current >= total - 3) {
            pages.push(1);
            pages.push('...');
            for (let i = total - 4; i <= total; i++) pages.push(i);
        } else {
            pages.push(1);
            pages.push('...');
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push('...');
            pages.push(total);
        }
    }
    
    return pages.filter(p => p !== '...');
});

const hasActiveFilters = computed(() => {
    return filters.category_id || filters.brand_id || filters.gender || 
           filters.price_min !== null || filters.price_max !== null;
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (filters.category_id) count++;
    if (filters.brand_id) count++;
    if (filters.gender) count++;
    if (filters.price_min !== null || filters.price_max !== null) count++;
    return count;
});

const resetFilters = () => {
    filters.category_id = '';
    filters.brand_id = '';
    filters.gender = '';
    filters.price_min = null;
    filters.price_max = null;
};

const totalPrice = computed(() => {
    return cart.value.reduce((sum, item) => {
        const price = getDiscountedPrice(item);
        return sum + (price * item.cartQuantity);
    }, 0);
});

const applyFilters = () => {
    // Фильтры применяются автоматически через computed
};

const toggleCart = () => {
    showCart.value = !showCart.value;
};

const openProductModal = (product) => {
    // Переход на страницу товара
    window.location.href = `/product?id=${product.id}`;
};

const closeProductModal = () => {
    productModal.open = false;
    productModal.product = null;
    selectedVariant.value = null;
    currentImageIndex.value = 0;
};

// Получить все изображения товара
const getAllImages = (product) => {
    if (!product) return [];
    const images = [];
    if (product.image) images.push(product.image);
    if (product.images && Array.isArray(product.images)) {
        images.push(...product.images);
    }
    return images;
};

// Получить текущее изображение
const getCurrentImage = (product) => {
    const images = getAllImages(product);
    return images[currentImageIndex.value] || '/placeholder.jpg';
};

// Получить метку пола
const getGenderLabel = (gender) => {
    const labels = {
        male: 'Мужской',
        female: 'Женский',
        unisex: 'Унисекс',
    };
    return labels[gender] || '';
};

const selectVariant = (variant) => {
    selectedVariant.value = variant;
};

// Вычисление цены со скидкой
const getDiscountedPrice = (product) => {
    if (!product || product.discount_type === 'none' || !product.discount_value) {
        return product?.price || 0;
    }

    if (product.discount_type === 'percent') {
        return product.price - (product.price * product.discount_value / 100);
    }

    if (product.discount_type === 'fixed') {
        return Math.max(0, product.price - product.discount_value);
    }

    return product.price;
};

// Форматирование скидки
const formatDiscount = (product) => {
    if (!product || product.discount_type === 'none' || !product.discount_value) {
        return '';
    }

    if (product.discount_type === 'percent') {
        return `${product.discount_value}%`;
    }

    return formatPrice(product.discount_value);
};

// Получение общего количества (теперь всегда из вариантов)
const getTotalQuantity = (product) => {
    if (!product) return 0;
    
    if (product.variants && product.variants.length > 0) {
        return product.variants.reduce((sum, v) => sum + v.quantity, 0);
    }
    
    return 0; // Если нет вариантов, товар недоступен
};

// Проверка возможности добавления в корзину
const canAddToCart = (product) => {
    if (!product) return false;
    
    // Размер теперь ВСЕГДА обязателен
    return selectedVariant.value && selectedVariant.value.quantity > 0;
};

// Текст кнопки добавления в корзину
const getAddToCartButtonText = (product) => {
    if (!product) return 'Недоступно';
    
    if (!selectedVariant.value) {
        return 'Выберите размер';
    }
    
    if (selectedVariant.value.quantity === 0) {
        return 'Нет в наличии';
    }
    
    return `Добавить в корзину (${selectedVariant.value.size})`;
};

// Добавление в корзину из модалки
const addToCartFromModal = (product) => {
    if (!canAddToCart(product)) return;
    
    const cartItem = {
        ...product,
        selectedSize: selectedVariant.value?.size || null,
        selectedVariantId: selectedVariant.value?.id || null,
        cartQuantity: 1,
    };
    
    // Проверяем, есть ли уже этот товар с таким размером в корзине
    const existingIndex = cart.value.findIndex(item => 
        item.id === product.id && item.selectedSize === cartItem.selectedSize
    );
    
    if (existingIndex >= 0) {
        cart.value[existingIndex].cartQuantity++;
    } else {
        cart.value.push(cartItem);
    }
    
    saveCart();
    closeProductModal();
};

const isInCart = (productId, size = null) => {
    if (size) {
        return cart.value.some(item => item.id === productId && item.selectedSize === size);
    }
    return cart.value.some(item => item.id === productId);
};

const getCartQuantity = (productId, size = null) => {
    if (size) {
        const item = cart.value.find(i => i.id === productId && i.selectedSize === size);
        return item ? item.cartQuantity : 0;
    }
    // Общее количество всех размеров этого товара
    return cart.value
        .filter(i => i.id === productId)
        .reduce((sum, item) => sum + item.cartQuantity, 0);
};

const addToCart = (product) => {
    if (!isInCart(product.id) && product.quantity > 0) {
        cart.value.push({
            ...product,
            cartQuantity: 1,
        });
        saveCart();
    }
};

const increaseQuantityFromCard = (productId) => {
    // Находим первый товар с этим ID (может быть несколько с разными размерами)
    const index = cart.value.findIndex(i => i.id === productId);
    if (index >= 0) {
        increaseQuantity(index);
    }
};

const decreaseQuantityFromCard = (productId) => {
    // Находим первый товар с этим ID (может быть несколько с разными размерами)
    const index = cart.value.findIndex(i => i.id === productId);
    if (index >= 0) {
        decreaseQuantity(index);
    }
};

const canIncreaseInCart = (productId) => {
    // Проверяем, можно ли увеличить количество товара в корзине
    const index = cart.value.findIndex(i => i.id === productId);
    if (index < 0) return false;
    
    const item = cart.value[index];
    return item.cartQuantity < getMaxQuantityForItem(item);
};

const getMaxQuantityForItem = (item) => {
    if (item.selectedSize && item.variants) {
        const variant = item.variants.find(v => v.size === item.selectedSize);
        return variant ? variant.quantity : 0;
    }
    return item.quantity || 0;
};

const removeFromCart = (index) => {
    cart.value.splice(index, 1);
    saveCart();
};

const increaseQuantity = (index) => {
    const item = cart.value[index];
    if (item && item.cartQuantity < getMaxQuantityForItem(item)) {
        item.cartQuantity++;
        saveCart();
    }
};

const decreaseQuantity = (index) => {
    const item = cart.value[index];
    if (item) {
        if (item.cartQuantity > 1) {
            item.cartQuantity--;
            saveCart();
        } else {
            removeFromCart(index);
        }
    }
};



const checkoutLoading = ref(false);

const checkout = async () => {
    const customerName = prompt('Введите ваше имя:');
    if (!customerName) return;

    const customerPhone = prompt('Введите ваш телефон:');
    if (!customerPhone) return;

    const customerEmail = prompt('Введите ваш email (необязательно):');

    checkoutLoading.value = true;

    try {
        const orderData = {
            customer_name: customerName,
            customer_phone: customerPhone,
            customer_email: customerEmail || null,
            items: cart.value.map(item => ({
                product_id: item.id,
                quantity: item.cartQuantity,
                size: item.selectedSize || null,
            })),
        };

        await axios.post('/api/orders', orderData);

        alert(`Заказ успешно оформлен!\n\nСумма: ${formatPrice(totalPrice.value)}\nТоваров: ${cartItemsCount.value}\n\nМы свяжемся с вами в ближайшее время.`);
        
        cart.value = [];
        saveCart();
        showCart.value = false;
        
        // Обновляем список товаров
        await fetchProducts();
    } catch (error) {
        alert(error?.response?.data?.error || 'Ошибка при оформлении заказа');
    } finally {
        checkoutLoading.value = false;
    }
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

// Проверка авторизации клиента
const checkCustomerAuth = async () => {
    const phone = localStorage.getItem('customer_phone');
    const customerData = localStorage.getItem('customer_data');
    
    if (phone && customerData) {
        isCustomerLoggedIn.value = true;
        customerPhone.value = phone;
        try {
            const data = JSON.parse(customerData);
            customerName.value = data.name || phone;
        } catch (e) {
            customerName.value = phone;
        }
        // Загружаем избранное для авторизованного пользователя
        await loadFavorites();
    }
};

const onAuthSuccess = async (customer) => {
    isCustomerLoggedIn.value = true;
    customerPhone.value = customer.phone;
    customerName.value = customer.name || customer.phone;
    await loadFavorites();
};

const goToProfile = () => {
    window.location.href = '/profile';
};

// Проверка, в избранном ли товар
const isInFavorites = (productId) => {
    return checkInFavorites(productId);
};

// Добавить/удалить из избранного
const toggleFavorite = async (productId) => {
    if (!isCustomerLoggedIn.value) {
        showAuthModal.value = true;
        return;
    }
    
    const { success: showSuccess } = useToast();
    
    if (isInFavorites(productId)) {
        await removeFromFavorites(productId);
        showSuccess('Товар удален из избранного');
    } else {
        await addToFavorites(productId);
        showSuccess('Товар добавлен в избранное');
    }
};

// Обработка добавления в корзину
const handleAddToCart = (product) => {
    // Если у товара несколько размеров - открываем модалку
    if (product.variants && product.variants.length > 1) {
        selectedProduct.value = product;
        showSizeModal.value = true;
    } 
    // Если один размер - добавляем сразу
    else if (product.variants && product.variants.length === 1) {
        const variant = product.variants[0];
        if (variant.quantity > 0) {
            selectedProduct.value = product;
            addToCartWithSize(variant);
        }
    }
    // Если нет вариантов - переходим на страницу товара
    else {
        router.push(`/product/${product.id}`);
    }
};

// Добавление в корзину с выбранным размером
const addToCartWithSize = (variant) => {
    const product = selectedProduct.value;
    if (!product) return;
    
    const cartItem = {
        ...product,
        selectedSize: variant.size,
        selectedVariantId: variant.id,
        cartQuantity: 1,
    };
    
    const existingIndex = cart.value.findIndex(item => 
        item.id === product.id && item.selectedSize === cartItem.selectedSize
    );
    
    if (existingIndex >= 0) {
        cart.value[existingIndex].cartQuantity++;
    } else {
        cart.value.push(cartItem);
    }
    
    saveCart();
    
    // Очищаем только если была модалка
    if (showSizeModal.value) {
        showSizeModal.value = false;
    }
    selectedProduct.value = null;
};

// Открытие модалки оформления заказа
const openCheckout = () => {
    showCart.value = false;
    showCheckoutModal.value = true;
};

// Успешное оформление заказа
const onOrderSuccess = async () => {
    clearCart();
    showCheckoutModal.value = false;
    // Обновляем список товаров
    await fetchProducts();
};

// Сброс страницы при изменении фильтров или сортировки
watch([filters, sortBy], () => {
    currentPage.value = 1;
}, { deep: true });

onMounted(async () => {
    await checkCustomerAuth();
    await Promise.all([fetchStores(), fetchCategories(), fetchBrands(), fetchProducts()]);
});
</script>

<style scoped>
.cart-enter-active,
.cart-leave-active {
    transition: opacity 0.3s ease;
}

.cart-enter-from,
.cart-leave-to {
    opacity: 0;
}

.cart-enter-active > div,
.cart-leave-active > div {
    transition: transform 0.3s ease;
}

.cart-enter-from > div {
    transform: translateX(100%);
}

.cart-leave-to > div {
    transform: translateX(100%);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.fade-enter-active > div,
.fade-leave-active > div {
    transition: all 0.3s ease;
}

.fade-enter-from > div {
    transform: scale(0.95);
    opacity: 0;
}

.fade-leave-to > div {
    transform: scale(0.95);
    opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
    transition: opacity 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
}

.slide-enter-active > div,
.slide-leave-active > div {
    transition: transform 0.3s ease;
}

.slide-enter-from > div {
    transform: translateY(100%);
}

.slide-leave-to > div {
    transform: translateY(100%);
}
</style>
