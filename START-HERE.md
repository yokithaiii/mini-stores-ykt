# 🚀 ДЕПЛОЙ ЗА 5 МИНУТ

## Шаг 1: Закоммить изменения (1 мин)

```bash
git add .
git commit -m "Production ready"
git push origin main
```

## Шаг 2: Railway (2 мин)

1. Открой: **https://railway.app**
2. Войди через GitHub
3. **"New Project"** → **"Deploy from GitHub repo"**
4. Выбери репозиторий `mini-stores-ykt`
5. Railway начнет деплой автоматически!

## Шаг 3: База данных (1 мин)

1. В проекте нажми **"+ New"**
2. **"Database"** → **"PostgreSQL"**
3. Готово! Railway автоматически подключит БД

## Шаг 4: Переменные (30 сек)

В разделе **"Variables"** добавь только:
```
APP_ENV=production
APP_DEBUG=false
```

Остальное Railway добавит сам!

## Шаг 5: Миграции (30 сек)

Когда деплой завершится, в Railway:
1. Открой вкладку проекта
2. Нажми на три точки → **"Run Command"**
3. Введи: `php artisan migrate --force`

## Шаг 6: Создать админа (1 мин)

В Railway "Run Command":
```bash
php artisan tinker
```

Затем введи:
```php
$user = new App\Models\User();
$user->email = 'admin@test.com';
$user->password = bcrypt('password123');
$user->firstname = 'Admin';
$user->save();
exit
```

## ✅ ГОТОВО!

Твой магазин работает! 🎉

**URL:** Смотри в Railway Dashboard → Settings → Domains

**Админка:** `твой-url.railway.app/auth`
- Email: `admin@test.com`
- Пароль: `password123`

---

## Если нужна помощь

📖 Подробная инструкция: `DEPLOYMENT.md`
⚡ Быстрый гайд: `QUICK-DEPLOY.md`
✅ Чеклист: `PRE-DEPLOY-CHECKLIST.md`

---

## Альтернативы Railway

### Render.com (тоже бесплатно)
1. https://render.com
2. "New" → "Web Service"
3. Подключи GitHub
4. Готово!

### Heroku (классика)
```bash
heroku create
heroku addons:create heroku-postgresql:mini
git push heroku main
heroku run php artisan migrate --force
```

---

## 🎯 Что дальше?

1. ✅ Смени пароль админа
2. ✅ Добавь товары
3. ✅ Настрой цвета в админке
4. ✅ Добавь свой домен (опционально)
5. ✅ Протестируй все функции

**Удачи! 🚀**
