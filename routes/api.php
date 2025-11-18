<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('auth')->group(function () {

    Route::post('/login-email', [AuthController::class, 'loginByEmail']);
    Route::post('/login-phone/code', [AuthController::class, 'loginByPhoneSendCode']);
    Route::post('/login-phone/confirm', [AuthController::class, 'loginByPhoneConfirmCode']);

    Route::post('/register/code', [AuthController::class, 'registerSendCode']);
    Route::post('/register/confirm', [AuthController::class, 'registerConfirmCode']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
    });

});

Route::get('/test', [TestController::class, 'test']);

// Public routes (без авторизации)
Route::prefix('stores')->group(function () {
    Route::get('/', [StoreController::class, 'index']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
});

Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
});

Route::prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/', [OrderController::class, 'index']); // Для клиентов тоже
});

// Customer auth routes (авторизация клиентов)
Route::prefix('customer')->group(function () {
    Route::post('/send-code', [CustomerAuthController::class, 'sendCode']);
    Route::post('/verify-code', [CustomerAuthController::class, 'verifyCode']);
    Route::post('/logout', [CustomerAuthController::class, 'logout']);
    
    // Требуют авторизации клиента (через заголовок X-Customer-Phone)
    Route::get('/profile', [CustomerAuthController::class, 'profile']);
    Route::post('/profile', [CustomerAuthController::class, 'updateProfile']);
});

// Favorites routes (для клиентов)
Route::prefix('favorites')->group(function () {
    Route::get('/', [FavoriteController::class, 'index']);
    Route::post('/', [FavoriteController::class, 'store']);
    Route::delete('/{productId}', [FavoriteController::class, 'destroy']);
    Route::get('/check/{productId}', [FavoriteController::class, 'check']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/auth/me', function (Request $request) {
        return $request->user();
    });

    // Stores routes (только для админов)
    Route::prefix('stores')->group(function () {
        Route::post('/', [StoreController::class, 'store']);
        Route::get('/{store}', [StoreController::class, 'show']);
        Route::post('/{store}', [StoreController::class, 'update']);
        Route::delete('/{store}', [StoreController::class, 'destroy']);
    });

    // Categories routes (только для админов)
    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{category}', [CategoryController::class, 'show']);
        Route::post('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });

    // Products routes (только для админов)
    Route::prefix('products')->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::post('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'destroy']);
    });

    // Orders routes (только для админов)
    Route::prefix('orders')->group(function () {
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/{order}/status', [OrderController::class, 'updateStatus']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });

    // Image upload routes (только для админов)
    Route::prefix('images')->group(function () {
        Route::post('/upload', [ImageController::class, 'upload']);
        Route::post('/delete', [ImageController::class, 'delete']);
    });

    // Brands routes (только для админов)
    Route::prefix('brands')->group(function () {
        Route::post('/', [BrandController::class, 'store']);
        Route::post('/{brand}', [BrandController::class, 'update']);
        Route::delete('/{brand}', [BrandController::class, 'destroy']);
    });

});
