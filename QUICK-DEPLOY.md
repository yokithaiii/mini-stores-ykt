# ⚡ Быстрый деплой за 5 минут

## Вариант 1: Railway (Самый простой) ⭐

### Шаг 1: Подготовка
```bash
# Закоммить все изменения
git add .
git commit -m "Ready for deployment"
git push
```

### Шаг 2: Railway
1. Открой [railway.app](https://railway.app)
2. Войди через GitHub
3. "New Project" → "Deploy from GitHub repo"
4. Выбери свой репозиторий
5. Railway автоматически всё настроит!

### Шаг 3: База данных
1. В проекте: "+ New" → "Database" → "PostgreSQL"
2. Railway автоматически подключит БД

### Шаг 4: Переменные (Variables)
Добавь только эти:
```env
APP_ENV=production
APP_DEBUG=false
```

Railway автоматически добавит:
- `APP_KEY` (сгенерирует сам)
- `DATABASE_URL` (из PostgreSQL)
- `PORT` (автоматически)

### Шаг 5: Миграции
В Railway Dashboard → вкладка "Deploy" → "View Logs"
Или через CLI:
```bash
railway run php artisan migrate --force
```

### Шаг 6: Создать админа
```bash
railway run php create-admin.php
```
Или через tinker:
```bash
railway run php artisan tinker
```
```php
User::create([
    'email' => 'admin@test.com',
    'password' => bcrypt('password'),
    'firstname' => 'Admin'
]);
```

### ✅ Готово!
Твой сайт доступен по адресу: `https://твой-проект.railway.app`

---

## Вариант 2: Render.com (Тоже простой)

### Шаг 1: Render
1. [render.com](https://render.com) → "New" → "Web Service"
2. Подключи GitHub
3. Настройки:
   - **Build**: `composer install --no-dev && npm ci && npm run build`
   - **Start**: `php artisan serve --host=0.0.0.0 --port=$PORT`

### Шаг 2: PostgreSQL
1. "New" → "PostgreSQL"
2. Подключи к Web Service

### Шаг 3: Переменные
```env
APP_ENV=production
APP_DEBUG=false
DATABASE_URL=${{Postgres.DATABASE_URL}}
```

### Шаг 4: Деплой
Render автоматически задеплоит!

---

## Вариант 3: Heroku (Классика)

```bash
# Установи Heroku CLI
heroku login

# Создай приложение
heroku create твой-магазин

# Добавь PostgreSQL
heroku addons:create heroku-postgresql:mini

# Деплой
git push heroku main

# Миграции
heroku run php artisan migrate --force

# Создать админа
heroku run php create-admin.php
```

---

## После деплоя

### Проверь:
1. ✅ Главная страница открывается
2. ✅ Админка `/auth` работает
3. ✅ Можно войти
4. ✅ Товары отображаются
5. ✅ Корзина работает

### Если что-то не работает:
```bash
# Railway
railway logs

# Render
Смотри логи в Dashboard

# Heroku
heroku logs --tail
```

---

## 🎉 Всё!

Твой магазин в продакшене!

**Важно:**
- Смени пароль админа после первого входа
- Настрой домен в настройках Railway/Render
- Добавь SSL (автоматически включен)

**Полезные ссылки:**
- Railway: https://railway.app
- Render: https://render.com
- Heroku: https://heroku.com

**Стоимость:**
- Railway: $5/месяц (500 часов бесплатно)
- Render: Бесплатно (с ограничениями)
- Heroku: $5-7/месяц
