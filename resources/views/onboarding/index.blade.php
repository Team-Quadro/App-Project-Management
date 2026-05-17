@section('title', 'Get Started')

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ProjectHub — Onboarding</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-canvas text-ink flex items-center justify-center p-4">

    {{-- Glowing background blob --}}
    <div class="fixed top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-primary/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-2xl z-10">
        {{-- Brand / Logo --}}
        <div class="flex items-center justify-center gap-2.5 mb-8">
            <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center shadow-lg shadow-primary/25">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <span class="text-xl font-bold tracking-tight text-ink">ProjectHub</span>
        </div>

        {{-- Hero Header --}}
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-ink tracking-tight">Setup Your Workspace</h2>
            <p class="text-[14px] text-ink-subtle mt-2 max-w-md mx-auto">To start collaborating, request to join an existing subsidiary or register a new one to create a custom tenant.</p>
        </div>

        {{-- Alerts --}}
        <x-auth-session-status class="mb-6" :status="session('status')" />
        @if(session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-[13px] font-medium flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-6">
            {{-- Pending Activation/Request State --}}
            @if($pendingRequest)
                <div class="v-card p-5 border-amber-500/20 bg-amber-500/5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center shrink-0 border border-amber-500/20">
                        <svg class="w-5 h-5 text-amber-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-[14px] font-semibold text-ink">Join Request Pending</h3>
                        <p class="text-[12px] text-ink-subtle mt-0.5">You have requested to join <span class="font-semibold text-ink">{{ $pendingRequest->tenant->company_name }}</span>. The subsidiary administrator has been notified and is reviewing your application.</p>
                    </div>
                    <span class="v-badge bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[11px] px-2.5 py-1 rounded">Awaiting Approval</span>
                </div>
            @endif

            {{-- Card Grid: Join or Register --}}
            <div class="grid grid-cols-1 gap-6">

                {{-- Register Subsidiary Card --}}
                <div class="v-card p-6 flex flex-col justify-between bg-surface-1 border border-hairline rounded-2xl shadow-xl">
                    <div>
                        <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/25 flex items-center justify-center text-cyan-400 mb-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.33l-7.5-5-7.5 5V21m16.5 0H3"/></svg>
                        </div>
                        <h3 class="text-[15px] font-bold text-ink">Succesfully Registering</h3>
                        <p class="text-[12px] text-ink-subtle mt-1.5 mb-5">Please wait, the administrator will approve you</p>
                    </div>
                </div>
            </div>

            {{-- Footer info --}}
            <div class="pt-6 border-t border-hairline/50 flex justify-between items-center text-[13px] text-ink-muted">
                <span>Account: <span class="text-ink-subtle font-medium">{{ auth()->user()->email }}</span></span>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-ink-subtle hover:text-red-400 transition-colors duration-100 font-semibold">
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
