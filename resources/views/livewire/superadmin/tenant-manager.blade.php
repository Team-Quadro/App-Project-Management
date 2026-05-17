<div>
    <x-slot name="header"><h1 class="text-sm font-semibold text-ink">Kelola Anak Perusahaan</h1></x-slot>
    <div class="space-y-4">
        @if(session('success'))<div class="p-3 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-[13px]">{{ session('success') }}</div>@endif
        <div class="flex items-center justify-between">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-subtle" />
                <input type="text" wire:model.live.debounce.300ms="search" class="v-input !py-1.5 pl-9 text-[13px] w-64" placeholder="Cari perusahaan...">
            </div>
            <button type="button" wire:click="openCreate" class="v-btn-primary text-[12px] gap-1.5"><x-heroicon-o-plus class="w-3.5 h-3.5"/>Tambah</button>
        </div>
        <x-modal wire:model="showForm" maxWidth="lg">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[15px] font-semibold text-ink">{{ $editingTenantId ? 'Edit Perusahaan' : 'Perusahaan Baru' }}</h3>
                    <button type="button" wire:click="$set('showForm', false)" class="text-ink-subtle hover:text-ink"><x-heroicon-o-x-mark class="w-5 h-5"/></button>
                </div>
                <form wire:submit="saveTenant" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Nama Perusahaan *</label><input type="text" wire:model="company_name" class="v-input w-full" required></div>
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Industri</label><input type="text" wire:model="industry" class="v-input w-full"></div>
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Nama PIC *</label><input type="text" wire:model="pic_name" class="v-input w-full" required></div>
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Email PIC *</label><input type="email" wire:model="pic_email" class="v-input w-full" required></div>
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Estimasi Pengguna</label><input type="number" wire:model="estimated_users" class="v-input w-full"></div>
                    </div>
                    <div class="flex justify-end pt-2"><button type="submit" class="v-btn-primary text-[13px] px-4 py-1.5">Simpan Perusahaan</button></div>
                </form>
            </div>
        </x-modal>
        <div class="v-card overflow-hidden">
            <table class="min-w-full text-sm"><thead><tr class="text-left border-b border-hairline text-ink-subtle text-[12px] uppercase tracking-wider"><th class="py-3 px-4 font-medium">Perusahaan</th><th class="py-3 px-4 font-medium">PIC</th><th class="py-3 px-4 font-medium">Status</th><th class="py-3 px-4 text-right"></th></tr></thead>
            <tbody class="divide-y divide-hairline">
                @forelse ($tenants as $tenant)
                <tr class="group hover:bg-surface-2/50 transition-colors" wire:key="t-{{ $tenant->id }}">
                    <td class="py-3 px-4"><div class="font-medium text-ink text-[14px]">{{ $tenant->company_name }}</div><div class="text-[11px] text-ink-muted">{{ $tenant->industry ?? '-' }}</div></td>
                    <td class="py-3 px-4"><div class="text-[13px] text-ink">{{ $tenant->pic_name }}</div><div class="text-[12px] text-ink-muted">{{ $tenant->pic_email }}</div></td>
                    <td class="py-3 px-4"><x-badge :variant="$tenant->status" size="xs">{{ ucfirst($tenant->status) }}</x-badge></td>
                    <td class="py-3 px-4"><div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button type="button" wire:click="editTenant({{ $tenant->id }})" class="p-1.5 text-ink-subtle hover:text-ink hover:bg-surface-3 rounded transition-colors" title="Edit"><x-heroicon-o-pencil-square class="w-4 h-4" /></button>
                        @if($tenant->status==='pending')<button type="button" x-data @click="$dispatch('open-confirm-modal', { id: 'approve-tenant-{{ $tenant->id }}' })" class="p-1.5 text-ink-subtle hover:text-green-500 hover:bg-surface-3 rounded" title="Setujui"><x-heroicon-o-check class="w-4 h-4" /></button>
                        <x-confirm-modal id="approve-tenant-{{ $tenant->id }}" wireClick="updateStatus({{ $tenant->id }}, 'approved')" title="Setujui Anak Perusahaan" message="Apakah Anda yakin ingin menyetujui perusahaan ini?" confirmText="Setujui" confirmStyle="primary" />@endif
                        <button type="button" x-data @click="$dispatch('open-confirm-modal', { id: 'delete-tenant-{{ $tenant->id }}' })" class="p-1.5 text-ink-subtle hover:text-red-400 hover:bg-surface-3 rounded" title="Hapus"><x-heroicon-o-trash class="w-4 h-4" /></button>
                        <x-confirm-modal id="delete-tenant-{{ $tenant->id }}" wireClick="deleteTenant({{ $tenant->id }})" title="Hapus Anak Perusahaan" message="Apakah Anda yakin ingin menghapus perusahaan {{ $tenant->company_name }} beserta semua datanya?" confirmText="Hapus" />
                    </div></td>
                </tr>
                @empty<tr><td class="py-8 px-4 text-center text-ink-subtle text-[13px]" colspan="4">Tidak ada data.</td></tr>@endforelse
            </tbody></table>
            <div class="p-4">{{ $tenants->links() }}</div>
        </div>
    </div>
</div>
