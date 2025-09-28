<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent', 'childs', 'store'])->get();

        return CategoryResource::collection($categories);
    }

    public function show(Category $category)
    {
        return response()->json(['data' => $category]);
    }

    public function store(CategoryRequest $request)
    {
        $validatedData = $request->validated();

        $category = Category::query()->create($validatedData);

        return response()->json(['data' => $category]);
    }

    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $validatedData = $request->validated();

        $category->update($validatedData);

        return response()->json(['data' => $category]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
