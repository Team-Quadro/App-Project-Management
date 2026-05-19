<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PMT') }} — @yield('title', 'Welcome')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-canvas text-ink">

    <div class="min-h-full flex">
        {{-- Left decorative panel --}}
        <div class="hidden lg:flex lg:w-[480px] xl:w-[560px] relative overflow-hidden bg-surface-1 border-r border-hairline flex-col justify-between p-12 shrink-0">
            {{-- Background grid pattern --}}
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(#5e6ad2 1px, transparent 1px), linear-gradient(to right, #5e6ad2 1px, transparent 1px); background-size: 32px 32px;"></div>
            {{-- Glow --}}
            <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <span class="text-lg font-bold text-ink tracking-tight">PMT</span>
                </div>
            </div>

            <div class="relative space-y-8">
                <div>
                    <h1 class="text-3xl font-bold text-ink leading-tight tracking-tight mb-3">Manage projects<br>with clarity.</h1>
                    <p class="text-[15px] text-ink-subtle leading-relaxed">A modern workspace for teams to plan, track, and ship work together.</p>
                </div>

                <div class="space-y-4">
                    @foreach(['Tenant-based team workspaces', 'Visual task tracking by status', 'Real-time project insights', 'Role-based access control'] as $feat)
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-[13px] text-ink-subtle">{{ $feat }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="relative text-[12px] text-ink-muted">
                &copy; {{ date('Y') }} PMT. All rights reserved.
            </div>
        </div>

        {{-- Right form panel --}}
        <div class="flex-1 flex flex-col items-center justify-center p-6 sm:p-12">
            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <div class="w-7 h-7 rounded-md bg-primary flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-base font-bold text-ink">PMT</span>
            </div>

            <div class="w-full max-w-sm">
                {{ $slot }}
            </div>
        </div>
    </div>

</body>
</html>