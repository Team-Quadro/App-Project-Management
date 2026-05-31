<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PMT') }} — 403 Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-canvas text-ink flex items-center justify-center p-4">

    <div class="max-w-md w-full px-8 py-10 bg-surface-1 border border-hairline rounded-2xl shadow-2xl text-center relative overflow-hidden">

        {{-- Efek Glow di background --}}
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Ikon Gembok (Lock) --}}
        <div class="w-20 h-20 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 border border-red-500/20 shadow-inner">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        {{-- Teks Error --}}
        <h1 class="text-4xl font-black text-ink mb-2 tracking-tight">403</h1>
        <h2 class="text-xl font-bold text-ink mb-3">Akses Terbatas</h2>

        {{-- Pesan Error Dinamis --}}
        <p class="text-[14px] text-ink-subtle mb-8 leading-relaxed">
            {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki izin otorisasi untuk mengakses halaman atau melakukan tindakan ini.' }}
        </p>

        {{-- Tombol Aksi --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="window.history.back()" class="px-5 py-2.5 bg-surface-2 hover:bg-surface-3 text-ink rounded-lg text-[13px] font-medium transition-colors border border-hairline flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </button>
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-lg text-[13px] font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Ke Dashboard
            </a>
        </div>
    </div>

</body>
</html>
