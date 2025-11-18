<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Mini Stores') }} · Интернет-магазин</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            @include('style')
        @endif
    </head>
    <body class="antialiased">
        <div id="app"></div>

        <noscript>
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-6">
                <div class="w-full max-w-lg rounded-2xl bg-white p-8 text-center shadow-2xl">
                    <p class="text-lg font-semibold text-gray-900">Для работы магазина нужен включённый JavaScript.</p>
                </div>
            </div>
        </noscript>
    </body>
</html>
