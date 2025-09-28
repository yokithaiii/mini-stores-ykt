<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
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

Route::middleware('auth:sanctum')->group(function () {

    // Stores routes
    Route::prefix('stores')->group(function () {

        Route::get('/', [StoreController::class, 'index']);
        Route::post('/', [StoreController::class, 'store']);
        Route::get('/{store}', [StoreController::class, 'show']);
        Route::post('/{store}', [StoreController::class, 'update']);
        Route::delete('/{store}', [StoreController::class, 'destroy']);

    });

    // Categories routes
    Route::prefix('categories')->group(function () {

        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{category}', [CategoryController::class, 'show']);
        Route::post('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);

    });

    // Products routes
    Route::prefix('products')->group(function () {

        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/{product}', [ProductController::class, 'show']);
        Route::post('/{product}', [ProductController::class, 'update']);
        Route::delete('/{product}', [ProductController::class, 'destroy']);

    });

});
