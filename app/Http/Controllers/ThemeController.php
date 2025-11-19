<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Получить настройки темы
     */
    public function index()
    {
        $settings = StoreSetting::whereIn('key', [
            'theme_primary_color',
            'theme_accent_color',
            'theme_success_color',
            'theme_warning_color',
            'theme_error_color',
        ])->get()->keyBy('key');

        return response()->json([
            'primary_color' => $settings->get('theme_primary_color')?->value ?? '#3b82f6',
            'accent_color' => $settings->get('theme_accent_color')?->value ?? '#22c55e',
            'success_color' => $settings->get('theme_success_color')?->value ?? '#22c55e',
            'warning_color' => $settings->get('theme_warning_color')?->value ?? '#f59e0b',
            'error_color' => $settings->get('theme_error_color')?->value ?? '#ef4444',
        ]);
    }
}
