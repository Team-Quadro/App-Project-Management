<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'ProjectHub') }} — @yield('title', 'Dashboard')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased bg-white text-gray-900" x-data="{ sidebarOpen: false }">
        <div class="flex h-full">
            {{-- Mobile overlay --}}
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/20 lg:hidden" x-cloak></div>

            {{-- Sidebar --}}
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-50 w-56 bg-white border-r border-border flex flex-col transition-transform duration-200 lg:translate-x-0 lg:static lg:z-auto">
                {{-- Brand --}}
                <div class="flex items-center gap-2.5 px-4 h-12 border-b border-border shrink-0">
                    <span class="text-sm md:text-xl font-bold text-gray-900">ProjectHub</span>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 overflow-y-auto sidebar-scroll px-2 py-2 space-y-0.5">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-[4px] text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-brand-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('projects.index') }}"
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-[4px] text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('projects.*') ? 'bg-brand-50 text-brand-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('projects.*') ? 'text-brand-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
                        Projects
                    </a>
                </nav>

                {{-- User --}}
                <div class="border-t border-border p-2 shrink-0">
                    <div class="flex items-center gap-2 px-2 py-1">
                        <div class="w-6 h-6 rounded-full bg-brand-600 flex items-center justify-center text-[10px] font-medium text-white shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-gray-400 truncate">{{ Auth::user()->role }}</p>
                        </div>
                    </div>
                    <div class="flex gap-1 mt-1.5">
                        <a href="{{ route('profile.edit') }}" class="flex-1 text-center text-[12px] font-medium text-gray-500 hover:text-brand-600 hover:bg-brand-50 rounded-[4px] py-1 transition-colors duration-100">Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full text-[12px] font-medium text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-[4px] py-1 transition-colors duration-100">Logout</button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Main --}}
            <div class="flex-1 flex flex-col min-w-0">
                <header class="h-12 bg-white border-b border-border flex items-center px-4 sm:px-6 shrink-0">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-1 -ml-1 mr-3 rounded-[4px] text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    @isset($header)
                        <div>{{ $header }}</div>
                    @endisset
                </header>

                <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-surface">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- Toast --}}
        <div x-data="toast()" x-init="init()" class="fixed top-3 right-3 z-[100] flex flex-col gap-2 w-full max-w-xs pointer-events-none">
            <template x-for="(t, i) in toasts" :key="i">
                <div x-show="t.visible" class="pointer-events-auto toast-enter" :class="{ 'toast-leave': !t.visible }">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-[4px] border bg-white shadow-sm text-[13px]"
                         :class="{ 'border-green-200 text-green-800': t.type==='success', 'border-red-200 text-red-800': t.type==='error', 'border-yellow-200 text-yellow-800': t.type==='warning', 'border-blue-200 text-blue-800': t.type==='info' }">
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
