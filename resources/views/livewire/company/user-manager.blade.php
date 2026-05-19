<div>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-ink">Anggota</h1>
    </x-slot>

    <div class="space-y-6">
        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-ink">Anggota Tim</h2>
                <p class="text-[13px] text-ink-subtle mt-0.5">Kelola anggota dari {{ $tenant?->company_name ?? 'perusahaan Anda' }}</p>
            </div>
            <button wire:click="$toggle('showCreate')" class="v-btn-primary text-[13px] gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Anggota
            </button>
        </div>

        {{-- Create Member Form --}}
        @if($showCreate)
        <div class="v-card p-5">
            <h3 class="text-[14px] font-semibold text-ink mb-3">Anggota Baru</h3>
            <form wire:submit="saveMember" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="v-label">Nama <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" class="v-input text-[13px]" placeholder="Nama lengkap" required />
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="v-label">Email <span class="text-red-500">*</span></label>
                        <input type="email" wire:model="email" class="v-input text-[13px]" placeholder="email@contoh.com" required />
                        @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="v-label">Jabatan</label>
                        <input type="text" wire:model="job_title" class="v-input text-[13px]" placeholder="Contoh: Developer" />
                        @error('job_title') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <p class="text-[12px] text-ink-muted">Kata sandi bawaan: <code class="text-ink-subtle bg-surface-3 px-1.5 py-0.5 rounded text-[11px]">password</code></p>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="v-btn-primary text-[13px]" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveMember">Buat Anggota</span>
                        <span wire:loading wire:target="saveMember">Menyimpan...</span>
                    </button>
                    <button type="button" wire:click="$toggle('showCreate')" class="v-btn-secondary text-[13px]">Batal</button>
                </div>
            </form>
        </div>
        @endif

        {{-- Members Table --}}
        <div class="v-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-hairline text-[11px] font-medium text-ink-subtle uppercase tracking-wider">
                            <th class="px-4 py-3">Anggota</th>
                            <th class="px-4 py-3">Jabatan</th>
                            <th class="px-4 py-3">Peran</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 w-20"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline/50">
                        @forelse ($members as $member)
                        <tr class="group hover:bg-surface-1/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-primary/20 text-primary flex items-center justify-center text-[11px] font-bold shrink-0">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-medium text-ink truncate">{{ $member->name }}</p>
                                        <p class="text-[11px] text-ink-muted truncate">{{ $member->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-[13px] text-ink-subtle">{{ $member->job_title ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $member->role === 'pic' ? 'bg-primary/15 text-primary' : 'bg-surface-3 text-ink-subtle' }}">
                                    {{ $member->role_label ?? ucfirst($member->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($member->approval_status === 'pending')
                                    <span class="inline-flex items-center gap-1 text-[11px] text-yellow-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Menunggu
                                    </span>
                                @elseif($member->approval_status === 'rejected')
                                    <span class="inline-flex items-center gap-1 text-[11px] text-red-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                                    </span>
                                @else
                                    @if($member->is_active)
                                    <span class="inline-flex items-center gap-1 text-[11px] text-green-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Aktif
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 text-[11px] text-ink-muted">
                                        <span class="w-1.5 h-1.5 rounded-full bg-ink-muted"></span> Tidak Aktif
                                    </span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($member->id !== auth()->id() && $member->role !== 'pic')
                                <div class="flex gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="toggleActive({{ $member->id }})" title="Aktif/Nonaktifkan" class="p-1.5 rounded-md hover:bg-surface-2 text-ink-subtle hover:text-ink transition-colors">
                                        @if($member->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                    <button type="button" x-data @click="$dispatch('open-confirm-modal', { id: 'delete-member-{{ $member->id }}' })" title="Hapus Anggota" class="p-1.5 rounded-md hover:bg-red-500/10 text-ink-subtle hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    <x-confirm-modal id="delete-member-{{ $member->id }}" wireClick="deleteMember({{ $member->id }})" title="Hapus Anggota" message="Anda yakin ingin menghapus anggota {{ $member->name }} dari tim?" confirmText="Hapus" />
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-[13px] text-ink-subtle">Tidak ada anggota ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>