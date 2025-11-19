<?php

use Illuminate\Support\Facades\Route;

// Главная страница
Route::view('/', 'shop')->name('shop');

// Авторизация
Route::view('/auth', 'auth')->name('login');

// Админ-панель (SPA)
Route::view('/admin/{any?}', 'admin')->where('any', '.*')->name('admin');

// SPA - все остальные роуты обрабатываются Vue Router
Route::view('/{any}', 'shop')->where('any', '^(?!admin|auth).*');
