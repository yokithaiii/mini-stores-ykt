<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return response()->json(['data' => $categories]);
    }

    public function show(Category $category)
    {
        return response()->json(['data' => $category]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'store_id' => 'required|string|exists:stores,id',
            'category_id' => 'nullable|string|exists:categories,id',
        ]);

        $category = Category::query()->create([
            'name' => $validatedData['name'],
            'store_id' => $validatedData['store_id'],
            'category_id' => $validatedData['category_id'],
        ]);

        return response()->json(['data' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validatedData);

        return response()->json(['data' => $category]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
