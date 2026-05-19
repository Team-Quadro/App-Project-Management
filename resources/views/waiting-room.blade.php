<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menunggu Persetujuan - ProjectHub</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-canvas text-ink flex items-center justify-center p-4">
    
    <div class="max-w-md w-full text-center space-y-8">
        <div class="flex items-center justify-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <span class="text-xl font-bold tracking-tight">ProjectHub</span>
        </div>

        <div class="v-card p-8 shadow-xl border border-hairline/50">
            <div class="w-16 h-16 bg-amber-500/10 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            
            <h2 class="text-xl font-semibold mb-2">Akun Sedang Diproses</h2>
            <p class="text-[14px] text-ink-subtle mb-8 leading-relaxed">
                Halo <strong>{{ auth()->user()->name }}</strong>, akun Anda saat ini sedang menunggu persetujuan dari Superadmin. Silakan cek kembali secara berkala atau hubungi administrator perusahaan Anda.
            </p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="v-btn-primary w-full justify-center py-2.5">
                    Kembali ke Halaman Login
                </button>
            </form>
        </div>
    </div>

</body>
</html>