<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Mini Stores Admin') }} · @yield('title', 'Панель')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            @include('style')
        @endif
    </head>
    <body class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-green-50/20 antialiased">
        <div class="min-h-screen w-full px-4 py-8">
            <div class="mx-auto w-full max-w-7xl space-y-6">
                @php
                    $navItems = [
                        ['label' => 'Дашборд', 'route' => 'admin.dashboard'],
                        ['label' => 'Категории', 'route' => 'admin.categories'],
                        ['label' => 'Товары', 'route' => 'admin.products'],
                        ['label' => 'Бренды', 'route' => 'admin.brands'],
                        ['label' => 'Заказы', 'route' => 'admin.orders'],
                    ];
                @endphp

                <header class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-blue-600">Mini Stores</p>
                            <h1 class="mt-2 text-2xl font-bold text-gray-900">Админ панель</h1>
                            <p class="mt-1 text-sm text-gray-600">
                                Управляйте своим магазином, категориями и товарами.
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 px-5 py-3 text-white shadow-sm">
                            <div>
                                <p id="admin-user-role" class="text-sm font-semibold">—</p>
                                <p id="admin-user-name" class="text-sm font-semibold">—</p>
                                <p id="admin-user-email" class="text-xs text-blue-100">—</p>
                            </div>
                            <button
                                class="inline-flex items-center rounded-lg bg-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/30"
                                data-logout="true"
                                type="button"
                            >
                                Выйти
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-2">
                        <a
                            href="{{ route('shop') }}"
                            class="rounded-xl px-5 py-2.5 text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                        >
                            ← Магазин
                        </a>
                        @foreach ($navItems as $item)
                            <a
                                href="{{ route($item['route']) }}"
                                class="rounded-xl px-5 py-2.5 text-sm font-semibold transition {{ request()->routeIs($item['route']) ? 'bg-blue-500 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            >
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </header>

                <main class="space-y-6">
                    @yield('content')
                </main>
            </div>
        </div>

        <noscript>
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-6">
                <div class="w-full max-w-lg rounded-3xl bg-white/90 p-8 text-center text-black shadow-2xl">
                    <p class="text-lg font-semibold">Для работы админки нужен включённый JavaScript.</p>
                </div>
            </div>
        </noscript>

        <script>
            (function () {
                const token = window.localStorage?.getItem('admin_token');
                if (!token) {
                    window.location.replace('/auth');
                    return;
                }

                const nameEl = document.getElementById('admin-user-name');
                const emailEl = document.getElementById('admin-user-email');
                const adminEl = document.getElementById('admin-user-role');

                fetch('/api/auth/me', {
                    headers: {
                        Accept: 'application/json',
                        Authorization: `Bearer ${token}`,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error('unauthorized');
                        }
                        return response.json();
                    })
                    .then((user) => {
                        const fullName = [user.firstname, user.lastname].filter(Boolean).join(' ').trim();
                        if (nameEl) {
                            nameEl.textContent = fullName || user.email || 'Администратор';
                        }
                        if (emailEl) {
                            emailEl.textContent = user.email ?? '—';
                        }
                        adminEl.textContent = 'Админ';
                    })
                    .catch(() => {
                        window.localStorage?.removeItem('admin_token');
                        window.location.replace('/auth');
                    });

                document.querySelectorAll('[data-logout]').forEach((button) => {
                    button.addEventListener('click', () => {
                        window.localStorage?.removeItem('admin_token');
                        window.location.replace('/auth');
                    });
                });
            })();
        </script>
    </body>
</html>

