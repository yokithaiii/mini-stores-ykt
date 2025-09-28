<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Http\Requests\StoreUpdateRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::all();

        return StoreResource::collection($stores);
    }

    public function show(Store $store)
    {
        return StoreResource::make($store);
    }

    public function store(StoreRequest $request)
    {
        $validatedData = $request->validated();

        $user = $request->user();

        $validatedData['user_id'] = $user->id;

        $store = Store::query()->create($validatedData);

        return StoreResource::make($store);
    }

    public function update(StoreUpdateRequest $request, Store $store)
    {
        $validatedData = $request->validated();

        $store->update($validatedData);

        return StoreResource::make($store);
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return response()->json(['message' => 'Store deleted successfully']);
    }
}
