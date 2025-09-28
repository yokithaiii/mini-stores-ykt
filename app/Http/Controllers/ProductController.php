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
        $products = Product::all();

        return response()->json(['data' => $products]);
    }

    public function show(Product $product)
    {
        return response()->json(['data' => $product]);
    }

    public function store(ProductRequest $request)
    {
        $validatedData = $request->validated();

        $product = Product::query()->create($validatedData);

        return response()->json(['data' => $product]);
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $validatedData = $request->validated();

        $product->update($validatedData);

        return response()->json(['data' => $product]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
