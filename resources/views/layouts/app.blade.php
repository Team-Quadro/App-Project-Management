<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'ProjectHub') }} — @yield('title', 'Dashboard')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased bg-canvas text-ink" x-data="{ sidebarOpen: false }">
        <div class="flex h-full">
            {{-- Mobile overlay --}}
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/20 lg:hidden" x-cloak></div>

            {{-- Sidebar --}}
                 <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                     class="fixed inset-y-0 left-0 z-50 w-56 bg-surface-1 border-r border-hairline flex flex-col transition-transform duration-200 lg:translate-x-0 lg:static lg:z-auto">
                {{-- Brand --}}
                <div class="flex items-center gap-2.5 px-4 h-12 border-b border-hairline shrink-0">
                    <span class="text-sm md:text-xl font-bold text-ink">ProjectHub</span>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 overflow-y-auto sidebar-scroll px-2 py-2 space-y-0.5">
                    <a href="{{ route('dashboard') }}" wire:navigate
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('dashboard') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                        <x-heroicon-o-squares-2x2 class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-ink-tertiary' }}" />
                        Dashboard
                    </a>
                    @if (Auth::user()->isSuperAdmin())
                        <a href="{{ route('superadmin.dashboard') }}" wire:navigate
                           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('superadmin.dashboard') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                            <x-heroicon-o-presentation-chart-bar class="w-4 h-4 {{ request()->routeIs('superadmin.dashboard') ? 'text-primary' : 'text-ink-tertiary' }}" />
                            Dashboard Holding
                        </a>
                        <a href="{{ route('superadmin.tenants.index') }}" wire:navigate
                           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('superadmin.tenants.*') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                            <x-heroicon-o-building-office class="w-4 h-4 {{ request()->routeIs('superadmin.tenants.*') ? 'text-primary' : 'text-ink-tertiary' }}" />
                            Anak Perusahaan
                        </a>
                        <a href="{{ route('superadmin.users.index') }}" wire:navigate
                           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('superadmin.users.*') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                            <x-heroicon-o-users class="w-4 h-4 {{ request()->routeIs('superadmin.users.*') ? 'text-primary' : 'text-ink-tertiary' }}" />
                            Pengguna
                        </a>
                    @endif
                    <a href="{{ route('projects.index') }}" wire:navigate
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('projects.*') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                        <x-heroicon-o-folder class="w-4 h-4 {{ request()->routeIs('projects.*') ? 'text-primary' : 'text-ink-tertiary' }}" />
                        Proyek
                    </a>
                    @if (Auth::user()->isCompanyAdmin())
                        <a href="{{ route('company.users.index') }}" wire:navigate
                           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('company.users.*') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                            <x-heroicon-o-user-group class="w-4 h-4 {{ request()->routeIs('company.users.*') ? 'text-primary' : 'text-ink-tertiary' }}" />
                            Anggota
                        </a>
                    @endif
                </nav>

                {{-- User --}}
                <div class="border-t border-hairline p-2 shrink-0">
                    <div class="flex items-center gap-2 px-2 py-1">
                        <div class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-[10px] font-medium text-white shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-medium text-ink truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-ink-subtle truncate">{{ Auth::user()->role_label }}</p>
                        </div>
                    </div>
                    <div class="flex gap-1 mt-1.5">
                        <a href="{{ route('profile.edit') }}" wire:navigate class="flex-1 text-center text-[12px] font-medium text-ink-subtle hover:text-primary hover:bg-surface-2 rounded-md py-1 transition-colors duration-100">Profil</a>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full text-[12px] font-medium text-ink-subtle hover:text-red-400 hover:bg-red-500/10 rounded-md py-1 transition-colors duration-100">Keluar</button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Main --}}
            <div class="flex-1 flex flex-col min-w-0">
                <header class="h-12 bg-surface-1 border-b border-hairline flex items-center justify-between px-4 sm:px-6 shrink-0">
                    <div class="flex items-center min-w-0">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-1 -ml-1 mr-3 rounded-md text-ink-subtle hover:bg-surface-2 hover:text-ink transition-colors duration-100">
                            <x-heroicon-o-bars-3 class="w-5 h-5" />
                        </button>
                        @isset($header)
                            <div>{{ $header }}</div>
                        @endisset
                    </div>


                </header>

                <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-canvas">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- Toast --}}
        <div x-data="toast()" x-init="init()" class="fixed top-3 right-3 z-[9999] flex flex-col gap-2 w-full max-w-xs pointer-events-none">
            <template x-for="(t, i) in toasts" :key="i">
                <div x-show="t.visible" class="pointer-events-auto toast-enter" :class="{ 'toast-leave': !t.visible }">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-md border bg-surface-1 shadow-sm text-[13px]"
                        :class="{ 'border-green-500/30 text-green-400': t.type==='success', 'border-red-500/30 text-red-300': t.type==='error', 'border-yellow-500/30 text-yellow-300': t.type==='warning', 'border-hairline text-ink': t.type==='info' }">
                        <p class="font-medium flex-1" x-text="t.message"></p>
                        <button @click="dismiss(i)" class="shrink-0 opacity-40 hover:opacity-100 transition-opacity">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <script>
        function toast() {
            return {
                toasts: [],
                init() {
                    @if (session('success')) this.show('success', @json(session('success'))); @endif
                    @if (session('error')) this.show('error', @json(session('error'))); @endif
                    @if (session('warning')) this.show('warning', @json(session('warning'))); @endif
                },
                show(type, message) {
                    const t = { type, message, visible: true };
                    this.toasts.push(t);
                    setTimeout(() => { this.dismiss(this.toasts.indexOf(t)); }, 3500);
                },
                dismiss(i) {
                    if (this.toasts[i]) {
                        this.toasts[i].visible = false;
                        setTimeout(() => { this.toasts.splice(i, 1); }, 200);
                    }
                }
            };
        }
        </script>
    </body>
</html>
