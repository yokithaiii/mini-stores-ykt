<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // Получить избранное
    public function index(Request $request)
    {
        $phone = $request->header('X-Customer-Phone');
        
        if (!$phone) {
            return response()->json(['error' => 'Не авторизован'], 401);
        }

        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json(['error' => 'Клиент не найден'], 404);
        }

        $favorites = Favorite::where('customer_id', $customer->id)
            ->with(['product.brand', 'product.variants'])
            ->get()
            ->pluck('product');

        return response()->json($favorites);
    }

    // Добавить в избранное
    public function store(Request $request)
    {
        $phone = $request->header('X-Customer-Phone');
        
        if (!$phone) {
            return response()->json(['error' => 'Не авторизован'], 401);
        }

        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json(['error' => 'Клиент не найден'], 404);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['error' => 'Товар не найден'], 404);
        }

        $favorite = Favorite::firstOrCreate([
            'customer_id' => $customer->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'message' => 'Товар добавлен в избранное',
            'favorite' => $favorite,
        ]);
    }

    // Удалить из избранного
    public function destroy(Request $request, $productId)
    {
        $phone = $request->header('X-Customer-Phone');
        
        if (!$phone) {
            return response()->json(['error' => 'Не авторизован'], 401);
        }

        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json(['error' => 'Клиент не найден'], 404);
        }

        $favorite = Favorite::where('customer_id', $customer->id)
            ->where('product_id', $productId)
            ->first();

        if (!$favorite) {
            return response()->json(['error' => 'Товар не в избранном'], 404);
        }

        $favorite->delete();

        return response()->json(['message' => 'Товар удален из избранного']);
    }

    // Проверить, в избранном ли товар
    public function check(Request $request, $productId)
    {
        $phone = $request->header('X-Customer-Phone');
        
        if (!$phone) {
            return response()->json(['is_favorite' => false]);
        }

        $customer = Customer::where('phone', $phone)->first();

        if (!$customer) {
            return response()->json(['is_favorite' => false]);
        }

        $isFavorite = Favorite::where('customer_id', $customer->id)
            ->where('product_id', $productId)
            ->exists();

        return response()->json(['is_favorite' => $isFavorite]);
    }
}
