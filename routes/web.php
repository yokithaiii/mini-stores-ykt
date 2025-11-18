<?php

use Illuminate\Support\Facades\Route;

// Главная страница
Route::view('/', 'shop')->name('shop');

// Админ-панель (без SPA)
Route::view('/auth', 'auth')->name('login');
Route::view('/admin', 'admin.dashboard')->name('admin.dashboard');
Route::view('/admin/categories', 'admin.categories')->name('admin.categories');
Route::view('/admin/products', 'admin.products')->name('admin.products');
Route::view('/admin/orders', 'admin.orders')->name('admin.orders');
Route::view('/admin/brands', 'admin.brands')->name('admin.brands');

// SPA - все остальные роуты обрабатываются Vue Router
Route::view('/{any}', 'shop')->where('any', '^(?!admin|auth).*$');
