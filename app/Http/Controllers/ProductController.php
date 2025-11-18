<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['variants', 'brand'])->get();

        // Добавляем вычисляемые поля
        $products->each(function ($product) {
            $product->discounted_price = $product->discounted_price;
            $product->total_quantity = $product->total_quantity;
        });

        return response()->json(['data' => $products]);
    }

    public function show(Product $product)
    {
        $product->load(['variants', 'brand']);
        $product->discounted_price = $product->discounted_price;
        $product->total_quantity = $product->total_quantity;

        return response()->json(['data' => $product]);
    }

    public function store(ProductRequest $request)
    {
        $validatedData = $request->validated();

        // Извлекаем варианты если есть
        $variants = $validatedData['variants'] ?? [];
        unset($validatedData['variants']);

        $product = Product::query()->create($validatedData);

        // Создаем варианты
        if (!empty($variants)) {
            foreach ($variants as $variant) {
                $product->variants()->create([
                    'size' => $variant['size'],
                    'quantity' => $variant['quantity'] ?? 0,
                ]);
            }
        }

        $product->load('variants');
        return response()->json(['data' => $product]);
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $validatedData = $request->validated();

        // Извлекаем варианты если есть
        $variants = $validatedData['variants'] ?? null;
        unset($validatedData['variants']);

        $product->update($validatedData);

        // Обновляем варианты
        if ($variants !== null) {
            // Удаляем старые варианты
            $product->variants()->delete();
            
            // Создаем новые
            if (!empty($variants)) {
                foreach ($variants as $variant) {
                    $product->variants()->create([
                        'size' => $variant['size'],
                        'quantity' => $variant['quantity'] ?? 0,
                    ]);
                }
            }
        }

        $product->load('variants');
        return response()->json(['data' => $product]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
