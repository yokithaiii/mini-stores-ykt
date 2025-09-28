<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return response()->json(['data' => $products]);
    }

    public function show(Product $product)
    {
        return response()->json(['data' => $product]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|integer|min:1',
            'store_id' => 'required|string|exists:stores,id',
            'category_id' => 'required|string|exists:categories,id',
        ]);

        $product = Product::query()->create([
            'name' => $validatedData['name'],
            'quantity' => $validatedData['quantity'],
            'price' => $validatedData['price'],
            'store_id' => $validatedData['store_id'],
            'category_id' => $validatedData['category_id'],
        ]);

        return response()->json(['data' => $product]);
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'price' => 'nullable|integer|min:1',
            'store_id' => 'nullable|string|exists:stores,id',
            'category_id' => 'nullable|string|exists:categories,id',
        ]);

        $product->update($validatedData);

        return response()->json(['data' => $product]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
