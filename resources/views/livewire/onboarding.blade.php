<div class="w-full max-w-2xl z-10">
    {{-- Brand --}}
    <div class="flex items-center justify-center gap-2.5 mb-8">
        <div class="w-9 h-9 rounded-xl bg-primary flex items-center justify-center shadow-lg shadow-primary/25">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <span class="text-xl font-bold tracking-tight text-ink">ProjectHub</span>
    </div>

    {{-- Hero --}}
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-ink tracking-tight">Siapkan Ruang Kerja Anda</h2>
        <p class="text-[14px] text-ink-subtle mt-2 max-w-md mx-auto">Untuk mulai berkolaborasi, ajukan bergabung ke anak perusahaan yang ada atau daftarkan yang baru.</p>
    </div>

    {{-- Alerts --}}
    @if(session('status'))
    <div class="mb-6 p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-[13px] font-medium">{{ session('status') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-[13px] font-medium flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="space-y-6">
        @if($pendingRequest)
        <div class="v-card p-5 border-amber-500/20 bg-amber-500/5 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center shrink-0 border border-amber-500/20">
                <svg class="w-5 h-5 text-amber-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-[14px] font-semibold text-ink">Permintaan Bergabung Tertunda</h3>
                <p class="text-[12px] text-ink-subtle mt-0.5">Anda telah mengajukan bergabung ke <span class="font-semibold text-ink">{{ $pendingRequest->tenant->company_name }}</span>. Administrator sedang meninjau aplikasi Anda.</p>
            </div>
            <span class="v-badge bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[11px] px-2.5 py-1 rounded">Menunggu Persetujuan</span>
        </div>
        @endif

        <div class="grid grid-cols-1 gap-6">
            <div class="v-card p-6 flex flex-col justify-between bg-surface-1 border border-hairline rounded-2xl shadow-xl">
                <div>
                    <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/25 flex items-center justify-center text-cyan-400 mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.33l-7.5-5-7.5 5V21m16.5 0H3"/></svg>
                    </div>
                    <h3 class="text-[15px] font-bold text-ink">Pendaftaran Berhasil</h3>
                    <p class="text-[12px] text-ink-subtle mt-1.5 mb-5">Silakan tunggu, administrator akan menyetujui akun Anda.</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="pt-6 border-t border-hairline/50 flex justify-between items-center text-[13px] text-ink-muted">
            <span>Akun: <span class="text-ink-subtle font-medium">{{ auth()->user()->email }}</span></span>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="text-ink-subtle hover:text-red-400 transition-colors duration-100 font-semibold">Keluar</button>
            </form>
        </div>
    </div>
</div>
