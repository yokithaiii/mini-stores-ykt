<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreSettingController extends Controller
{
    public function index()
    {
        $settings = StoreSetting::all()->groupBy('group');
        
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $setting) {
            StoreSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
            
            // Синхронизируем с моделью Store
            if ($setting['key'] === 'store_name') {
                $store = Store::first();
                if ($store) {
                    $store->update(['name' => $setting['value']]);
                }
            }
            
            if ($setting['key'] === 'store_description') {
                $store = Store::first();
                if ($store) {
                    $store->update(['description' => $setting['value']]);
                }
            }
        }

        return response()->json([
            'message' => 'Настройки успешно обновлены',
        ]);
    }

    public function getPublic()
    {
        // Публичные настройки для клиентской части
        // Берем из Store если есть, иначе из StoreSetting
        $store = Store::first();
        $settings = StoreSetting::whereIn('group', ['general', 'contact', 'social'])->get()->pluck('value', 'key')->toArray();
        
        // Переопределяем название и описание из Store если есть
        if ($store) {
            $settings['store_name'] = $store->name;
            $settings['store_description'] = $store->description;
        }
        
        return response()->json($settings);
    }
}
