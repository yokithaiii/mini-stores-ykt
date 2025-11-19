<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Helpers\PhoneHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    // Отправка кода (пока просто 1111)
    public function sendCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
            'name' => 'required|string|max:255',
        ]);

        // Нормализуем номер телефона
        $phone = PhoneHelper::normalize($request->phone);
        
        if (!PhoneHelper::isValid($phone)) {
            return response()->json(['error' => 'Неверный формат номера телефона'], 400);
        }
        
        // Находим или создаем клиента
        $customer = Customer::firstOrCreate(
            ['phone' => $phone],
            ['name' => $request->name, 'email' => null]
        );

        // Генерируем код (пока всегда 1111)
        $code = '1111';
        
        $customer->update([
            'verification_code' => Hash::make($code),
            'code_expires_at' => now()->addMinutes(10),
        ]);

        // В реальности здесь отправка СМС
        // SMS::send($phone, "Ваш код: $code");

        return response()->json([
            'message' => 'Код отправлен на номер ' . $phone,
            'dev_code' => $code, // Только для разработки!
        ]);
    }

    // Проверка кода и вход
    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:4',
        ]);

        // Нормализуем номер телефона
        $phone = PhoneHelper::normalize($request->phone);
        
        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json(['error' => 'Клиент не найден'], 404);
        }

        if (!$customer->verification_code || !$customer->code_expires_at) {
            return response()->json(['error' => 'Код не был отправлен'], 400);
        }

        if (now()->isAfter($customer->code_expires_at)) {
            return response()->json(['error' => 'Код истек'], 400);
        }

        if (!Hash::check($request->code, $customer->verification_code)) {
            return response()->json(['error' => 'Неверный код'], 400);
        }

        // Очищаем код после успешной проверки
        $customer->update([
            'verification_code' => null,
            'code_expires_at' => null,
        ]);

        // Генерируем токен
        $token = Str::random(60);

        return response()->json([
            'message' => 'Успешный вход',
            'token' => $token,
            'customer' => $customer,
        ]);
    }

    // Получить профиль
    public function profile(Request $request)
    {
        $phone = $request->header('X-Customer-Phone');
        
        if (!$phone) {
            return response()->json(['error' => 'Не авторизован'], 401);
        }

        // Нормализуем номер телефона
        $phone = PhoneHelper::normalize($phone);
        
        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json(['error' => 'Клиент не найден'], 404);
        }

        return response()->json($customer);
    }

    // Обновить профиль
    public function updateProfile(Request $request)
    {
        $phone = $request->header('X-Customer-Phone');
        
        if (!$phone) {
            return response()->json(['error' => 'Не авторизован'], 401);
        }

        // Нормализуем номер телефона
        $phone = PhoneHelper::normalize($phone);
        
        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json(['error' => 'Клиент не найден'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $customer->update($request->only(['name', 'email']));

        return response()->json([
            'message' => 'Профиль обновлен',
            'customer' => $customer,
        ]);
    }

    // Выход
    public function logout(Request $request)
    {
        return response()->json(['message' => 'Выход выполнен']);
    }
}
