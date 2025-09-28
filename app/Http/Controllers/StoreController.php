<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::all();

        return response()->json(['data' => $stores]);
    }

    public function show(Store $store)
    {
        return response()->json(['data' => $store]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'logo_url' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        $store = Store::query()->create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'logo_url' => $validatedData['logo_url'],
            'user_id' => $user->id,
        ]);

        return response()->json(['data' => $store]);
    }

    public function update(Request $request, Store $store)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'logo_url' => 'nullable|string|max:255',
        ]);

        $store->update($validatedData);

        return response()->json(['data' => $store]);
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return response()->json(['message' => 'Store deleted successfully']);
    }
}
