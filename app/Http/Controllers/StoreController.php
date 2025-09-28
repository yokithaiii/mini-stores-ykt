<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Http\Requests\StoreUpdateRequest;
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

    public function store(StoreRequest $request)
    {
        $validatedData = $request->validated();

        $user = $request->user();

        $validatedData['user_id'] = $user->id;

        $store = Store::query()->create($validatedData);

        return response()->json(['data' => $store]);
    }

    public function update(StoreUpdateRequest $request, Store $store)
    {
        $validatedData = $request->validated();

        $store->update($validatedData);

        return response()->json(['data' => $store]);
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return response()->json(['message' => 'Store deleted successfully']);
    }
}
