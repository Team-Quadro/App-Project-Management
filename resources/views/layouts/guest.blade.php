<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ProjectHub') }} — @yield('title', 'Welcome')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full font-sans antialiased bg-surface">
    <div class="min-h-full flex items-center justify-center p-4">
        <div class="w-full max-w-sm">
            <div class="v-card p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>