<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // max 5MB
        ]);

        try {
            $image = $request->file('image');
            
            // Генерируем уникальное имя файла
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            
            // Сохраняем в public/images/products
            $path = $image->storeAs('images/products', $filename, 'public');
            
            // Возвращаем URL изображения
            $url = Storage::url($path);
            
            return response()->json([
                'message' => 'Изображение загружено',
                'url' => $url,
                'path' => $path,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Не удалось загрузить изображение: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        try {
            $path = $request->input('path');
            
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                
                return response()->json([
                    'message' => 'Изображение удалено',
                ]);
            }
            
            return response()->json([
                'error' => 'Изображение не найдено',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Не удалось удалить изображение: ' . $e->getMessage(),
            ], 400);
        }
    }
}
