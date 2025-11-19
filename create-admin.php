<?php

/**
 * Быстрое создание админа для продакшена
 * Запуск: php create-admin.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "🔐 Создание администратора\n\n";

$email = readline("Email: ");
$password = readline("Пароль: ");
$firstname = readline("Имя: ");
$lastname = readline("Фамилия (опционально): ");

try {
    $user = User::create([
        'email' => $email,
        'password' => bcrypt($password),
        'firstname' => $firstname,
        'lastname' => $lastname ?: null,
    ]);

    echo "\n✅ Администратор создан успешно!\n";
    echo "Email: {$user->email}\n";
    echo "ID: {$user->id}\n";
    echo "\nТеперь можете войти в админку: /auth\n";
} catch (Exception $e) {
    echo "\n❌ Ошибка: " . $e->getMessage() . "\n";
}
