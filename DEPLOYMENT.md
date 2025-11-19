# 🚀 Деплой Mini Stores

## Быстрый деплой на Railway (Рекомендуется)

### 1. Регистрация
1. Перейди на [railway.app](https://railway.app)
2. Зарегистрируйся через GitHub

### 2. Создание проекта
1. Нажми "New Project"
2. Выбери "Deploy from GitHub repo"
3. Выбери свой репозиторий
4. Railway автоматически определит Laravel

### 3. Добавление базы данных
1. В проекте нажми "+ New"
2. Выбери "Database" → "PostgreSQL"
3. Railway автоматически создаст БД и подключит её

### 4. Настройка переменных окружения
В разделе "Variables" добавь:

```env
APP_NAME="Mini Stores"
APP_ENV=production
APP_KEY=base64:СГЕНЕРИРУЙ_КЛЮЧ
APP_DEBUG=false
APP_URL=https://твой-домен.railway.app

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 5. Генерация APP_KEY
Локально выполни:
```bash
php artisan key:generate --show
```
Скопируй результат в переменную `APP_KEY`

### 6. Деплой
1. Railway автоматически задеплоит при пуше в main
2. Или нажми "Deploy" вручную

### 7. Запуск миграций
В Railway CLI или через Dashboard:
```bash
php artisan migrate --force
```

### 8. Создание админа
```bash
php artisan tinker
```
Затем:
```php
$user = new App\Models\User();
$user->email = 'admin@example.com';
$user->password = bcrypt('password');
$user->firstname = 'Admin';
$user->save();
```

---

## Альтернатива: Render.com

### 1. Создание Web Service
1. Перейди на [render.com](https://render.com)
2. "New" → "Web Service"
3. Подключи GitHub репозиторий

### 2. Настройки
- **Build Command**: `composer install --no-dev && npm ci && npm run build`
- **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`
- **Environment**: `PHP`

### 3. Добавь PostgreSQL
1. "New" → "PostgreSQL"
2. Подключи к Web Service

### 4. Переменные окружения
Добавь те же переменные, что и для Railway

---

## Альтернатива: Vercel (только для фронтенда)

Если хочешь разделить фронт и бэк:

### Backend на Railway
Следуй инструкциям выше

### Frontend на Vercel
1. Создай отдельный репозиторий для фронтенда
2. Скопируй `resources/js` и `public`
3. Деплой на Vercel
4. Настрой API URL на Railway backend

---

## Локальная подготовка перед деплоем

```bash
# 1. Очистка
php artisan config:clear
php artisan cache:clear

# 2. Установка зависимостей
composer install --no-dev --optimize-autoloader
npm ci

# 3. Сборка
npm run build

# 4. Оптимизация
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Тест
php artisan serve
```

---

## Проверка после деплоя

1. ✅ Открывается главная страница
2. ✅ Работает авторизация админа
3. ✅ Загружаются товары
4. ✅ Работает корзина
5. ✅ Оформление заказа
6. ✅ Загрузка изображений

---

## Troubleshooting

### Ошибка 500
- Проверь `APP_KEY` в переменных окружения
- Проверь подключение к БД
- Посмотри логи: `php artisan log:tail`

### Не работают стили
- Проверь, что `npm run build` выполнился
- Проверь `APP_URL` в .env

### Не загружаются изображения
- Выполни `php artisan storage:link`
- Проверь права на папку `storage`

### База данных пустая
- Выполни `php artisan migrate --force`
- Создай тестовые данные через tinker

---

## 🎉 Готово!

Твой магазин теперь в продакшене!

**Полезные команды:**
```bash
# Логи
php artisan log:tail

# Очистка кэша
php artisan cache:clear

# Перезапуск
railway restart (или через Dashboard)
```
