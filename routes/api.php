<?php

use App\Http\Controllers\AuthController;
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
    Route::prefix('companies')->group(function () {

//        Route::get('/',)

    });

});
