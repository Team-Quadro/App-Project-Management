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
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('dashboard') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-ink-tertiary' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                        Dashboard
                    </a>
                    @if (Auth::user()->isSuperAdmin())
                        <a href="{{ route('superadmin.dashboard') }}"
                           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('superadmin.dashboard') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('superadmin.dashboard') ? 'text-primary' : 'text-ink-tertiary' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.5h19.5m-19.5 9h19.5M6 3.75v16.5M18 3.75v16.5"/></svg>
                            Holding Dashboard
                        </a>
                        <a href="{{ route('superadmin.tenants.index') }}"
                           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('superadmin.tenants.*') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('superadmin.tenants.*') ? 'text-primary' : 'text-ink-tertiary' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 7.5h15M6 7.5V21m12-13.5V21M7.5 3h9l1.5 4.5H6L7.5 3z"/></svg>
                            Tenants
                        </a>
                        <a href="{{ route('superadmin.users.index') }}"
                           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('superadmin.users.*') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('superadmin.users.*') ? 'text-primary' : 'text-ink-tertiary' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            Users
                        </a>
                    @endif
                    <a href="{{ route('projects.index') }}"
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('projects.*') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('projects.*') ? 'text-primary' : 'text-ink-tertiary' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
                        Projects
                    </a>
                    @if (Auth::user()->isCompanyAdmin())
                        <a href="{{ route('company.users.index') }}"
                           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-[13px] font-medium transition-colors duration-100 {{ request()->routeIs('company.users.*') ? 'bg-surface-2 text-ink' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                            <svg class="w-4 h-4 {{ request()->routeIs('company.users.*') ? 'text-primary' : 'text-ink-tertiary' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            Members
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
                        <a href="{{ route('profile.edit') }}" class="flex-1 text-center text-[12px] font-medium text-ink-subtle hover:text-primary hover:bg-surface-2 rounded-md py-1 transition-colors duration-100">Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full text-[12px] font-medium text-ink-subtle hover:text-red-400 hover:bg-red-500/10 rounded-md py-1 transition-colors duration-100">Logout</button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Main --}}
            <div class="flex-1 flex flex-col min-w-0">
                <header class="h-12 bg-surface-1 border-b border-hairline flex items-center justify-between px-4 sm:px-6 shrink-0">
                    <div class="flex items-center min-w-0">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-1 -ml-1 mr-3 rounded-md text-ink-subtle hover:bg-surface-2 hover:text-ink transition-colors duration-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        @isset($header)
                            <div>{{ $header }}</div>
                        @endisset
                    </div>

                    {{-- Superadmin subsidiary switcher --}}
                    @if (Auth::user()->isSuperAdmin())
                    @php
                        $allTenants = \App\Models\Tenant::orderBy('company_name')->get();
                        $activeTenantId = session('superadmin_tenant_id');
                        $activeTenant = $activeTenantId ? $allTenants->firstWhere('id', $activeTenantId) : null;
                    @endphp
                    <div x-data="{ open: false }" class="relative shrink-0 ml-4">
                        <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[12px] font-medium border transition-colors duration-100
                            {{ $activeTenant ? 'bg-primary/10 border-primary/30 text-primary' : 'bg-surface-2 border-hairline text-ink-subtle hover:text-ink' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 7.5h15M6 7.5V21m12-13.5V21M7.5 3h9l1.5 4.5H6L7.5 3z"/></svg>
                            <span class="truncate max-w-[160px]">{{ $activeTenant?->company_name ?? 'All Subsidiaries' }}</span>
                            <svg class="w-3 h-3 shrink-0 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-1 w-56 bg-surface-1 border border-hairline rounded-lg shadow-xl z-50 py-1 overflow-hidden" x-cloak>
                            <div class="px-3 py-2 border-b border-hairline">
                                <p class="text-[11px] font-semibold uppercase tracking-wider text-ink-muted">Switch Subsidiary</p>
                            </div>
                            <div class="max-h-64 overflow-y-auto sidebar-scroll">
                                {{-- All subsidiaries option --}}
                                <form method="POST" action="{{ route('superadmin.switch-tenant') }}">
                                    @csrf
                                    <input type="hidden" name="tenant_id" value="">
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-[13px] text-left transition-colors duration-100 {{ !$activeTenantId ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                        All Subsidiaries
                                        @unless($activeTenantId)
                                        <svg class="w-3 h-3 ml-auto text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @endunless
                                    </button>
                                </form>

                                @foreach ($allTenants as $t)
                                <form method="POST" action="{{ route('superadmin.switch-tenant') }}">
                                    @csrf
                                    <input type="hidden" name="tenant_id" value="{{ $t->id }}">
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-[13px] text-left transition-colors duration-100 {{ $activeTenantId == $t->id ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                                        <span class="w-5 h-5 rounded bg-surface-3 flex items-center justify-center text-[10px] font-bold text-ink-muted shrink-0">{{ strtoupper(substr($t->company_name, 0, 1)) }}</span>
                                        <span class="truncate">{{ $t->company_name }}</span>
                                        @if($activeTenantId == $t->id)
                                        <svg class="w-3 h-3 ml-auto text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </button>
                                </form>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </header>

                <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-canvas">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- Toast --}}
        <div x-data="toast()" x-init="init()" class="fixed top-3 right-3 z-100 flex flex-col gap-2 w-full max-w-xs pointer-events-none">
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
