# Миграция на SPA (Single Page Application)

## Что изменилось

Приложение теперь работает как SPA с использованием Vue Router. Это означает:
- ✅ Переходы между страницами без перезагрузки
- ✅ Плавная навигация
- ✅ Быстрая работа
- ✅ Лучший UX

---

## Архитектура

### Главный компонент
**App.vue** - корневой компонент с `<router-view />`

### Роутер
**router/index.js** - конфигурация маршрутов:
- `/` - главная страница (ShopApp)
- `/product/:id` - страница товара (ProductDetailApp)
- `/profile` - профиль клиента (CustomerProfileApp, требует авторизации)

### Роуты Laravel
**routes/web.php**:
```php
// SPA - все роуты обрабатываются Vue Router
Route::view('/{any}', 'shop')->where('any', '^(?!admin|auth).*$');

// Админ-панель остается без SPA
Route::view('/admin', 'admin.dashboard');
// ...
```

---

## Навигация

### Вместо обычных ссылок используем router-link:

**Было:**
```vue
<a href="/product?id=123">Товар</a>
```

**Стало:**
```vue
<router-link :to="`/product/${product.id}`">Товар</router-link>
```

### Программная навигация:

**Было:**
```javascript
window.location.href = '/profile';
```

**Стало:**
```javascript
import { useRouter } from 'vue-router';
const router = useRouter();
router.push('/profile');
```

---

## Защита роутов

Роут `/profile` защищен проверкой авторизации:

```javascript
router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth) {
        const customerPhone = localStorage.getItem('customer_phone');
        if (!customerPhone) {
            next('/'); // Редирект на главную
        } else {
            next();
        }
    } else {
        next();
    }
});
```

---

## Scroll Behavior

При переходе между страницами автоматически прокручивается наверх:

```javascript
scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
        return savedPosition; // Восстанавливаем позицию при навигации назад
    } else {
        return { top: 0 }; // Прокручиваем наверх
    }
}
```

---

## Получение параметров роута

### В ProductDetailApp.vue:

**Было:**
```javascript
const productId = new URLSearchParams(window.location.search).get('id');
```

**Стало:**
```javascript
import { useRoute } from 'vue-router';
const route = useRoute();
const productId = computed(() => route.params.id);
```

---

## Компоненты

### Обновленные компоненты:

1. **App.vue** (новый) - главный компонент
2. **AppHeader.vue** - использует `router-link` и `useRouter()`
3. **ShopApp.vue** - использует `router-link` и `useRouter()`
4. **ProductDetailApp.vue** - использует `useRoute()` для получения ID
5. **CustomerProfileApp.vue** - использует `router-link` и `useRouter()`

### Админ-панель:
Остается без изменений, работает как раньше (без SPA)

---

## Преимущества

### Для пользователей:
- ⚡ Мгновенные переходы между страницами
- 🎨 Плавные анимации
- 📱 Лучше работает на мобильных
- 💾 Меньше трафика (не загружаются повторно CSS/JS)

### Для разработчиков:
- 🔧 Проще управлять состоянием
- 🎯 Централизованная навигация
- 🛡️ Защита роутов из одного места
- 📊 Легче отслеживать переходы

---

## Тестирование

### Проверьте:
1. ✅ Переход с главной на страницу товара
2. ✅ Переход на профиль (с авторизацией)
3. ✅ Переход на профиль без авторизации (должен редиректить на главную)
4. ✅ Кнопка "Назад" в браузере
5. ✅ Прямой переход по URL (например, `/product/123`)
6. ✅ Обновление страницы (F5)

---

## Возможные проблемы

### Проблема: 404 при обновлении страницы

**Решение:** Убедитесь, что Laravel роут настроен правильно:
```php
Route::view('/{any}', 'shop')->where('any', '^(?!admin|auth).*$');
```

### Проблема: Не работают переходы

**Решение:** Проверьте, что используете `router-link` вместо `<a>` и `router.push()` вместо `window.location.href`

### Проблема: Состояние не сохраняется

**Решение:** Используйте Pinia или Vuex для глобального состояния, или localStorage для персистентности

---

## Дальнейшие улучшения

### Можно добавить:
- 🎭 Анимации переходов между страницами
- 📦 Lazy loading компонентов
- 🔄 Prefetching данных
- 📱 PWA функционал
- 🎨 Transition компоненты Vue

### Пример анимации переходов:
```vue
<router-view v-slot="{ Component }">
    <transition name="fade" mode="out-in">
        <component :is="Component" />
    </transition>
</router-view>
```

---

## Команды

### Установка зависимостей:
```bash
npm install vue-router@4
```

### Сборка:
```bash
npm run build
```

### Разработка:
```bash
npm run dev
```

---

**Дата миграции:** 18 ноября 2025  
**Версия Vue Router:** 4.x  
**Статус:** ✅ Готово к использованию
