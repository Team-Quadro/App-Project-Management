<div>
    <x-slot name="header"><h1 class="text-sm font-semibold text-ink">Kelola Pengguna</h1></x-slot>
    <div class="space-y-4">
        @if(session('success'))<div class="p-3 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 text-[13px]">{{ session('success') }}</div>@endif
        <div class="flex items-center justify-between">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-subtle" />
                <input type="text" wire:model.live.debounce.300ms="search" class="v-input !py-1.5 !pl-10 bg-surface-1 border-transparent hover:border-hairline focus:border-primary w-full text-[13px] transition-colors" placeholder="Cari pengguna...">
            </div>
            <button type="button" wire:click="$toggle('showCreate')" class="v-btn-primary text-[12px] gap-1.5"><x-heroicon-o-plus class="w-3.5 h-3.5"/>Tambah Pengguna</button>
        </div>
        <x-modal wire:model="showCreate" maxWidth="lg">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[15px] font-semibold text-ink">Pengguna Baru</h3>
                    <button type="button" wire:click="$toggle('showCreate')" class="text-ink-subtle hover:text-ink"><x-heroicon-o-x-mark class="w-5 h-5"/></button>
                </div>
                <form wire:submit="createUser" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Nama</label><input type="text" wire:model="createName" class="v-input w-full" required></div>
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Email</label><input type="email" wire:model="createEmail" class="v-input w-full" required></div>
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Password</label><input type="password" wire:model="createPassword" class="v-input w-full" required></div>
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Peran</label><select wire:model="createRole" class="v-select w-full" required><option value="superadmin">Superadmin</option><option value="pic">PIC</option><option value="member">Anggota</option></select></div>
                        <div class="space-y-1.5 col-span-2"><label class="text-[12px] font-medium text-ink-subtle">Perusahaan (Opsional)</label><select wire:model="createTenantId" class="v-select w-full"><option value="">- Tanpa Perusahaan -</option>@foreach($tenants as $t)<option value="{{ $t->id }}">{{ $t->company_name }}</option>@endforeach</select></div>
                        <div class="space-y-1.5 col-span-2"><label class="inline-flex items-center gap-2 text-[12px]"><input type="checkbox" wire:model="createIsActive" class="rounded-sm border-hairline text-primary bg-canvas"/>Aktif</label></div>
                    </div>
                    <div class="flex justify-end pt-2"><button type="submit" class="v-btn-primary text-[13px] px-4 py-1.5">Simpan Pengguna</button></div>
                </form>
            </div>
        </x-modal>
        <x-modal wire:model="showEdit" maxWidth="lg">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[15px] font-semibold text-ink">Edit Pengguna</h3>
                    <button type="button" wire:click="cancelEdit" class="text-ink-subtle hover:text-ink"><x-heroicon-o-x-mark class="w-5 h-5"/></button>
                </div>
                <form wire:submit="saveEdit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Nama</label><input type="text" wire:model="editName" class="v-input w-full" required></div>
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Email</label><input type="email" wire:model="editEmail" class="v-input w-full" required></div>
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Peran</label><select wire:model="editRole" class="v-select w-full" required><option value="superadmin">Superadmin</option><option value="pic">PIC</option><option value="member">Anggota</option></select></div>
                        <div class="space-y-1.5"><label class="text-[12px] font-medium text-ink-subtle">Perusahaan</label><select wire:model="editTenantId" class="v-select w-full"><option value="">- Tanpa Perusahaan -</option>@foreach($tenants as $t)<option value="{{ $t->id }}">{{ $t->company_name }}</option>@endforeach</select></div>
                        <div class="space-y-1.5 col-span-2"><label class="inline-flex items-center gap-2 text-[12px]"><input type="checkbox" wire:model="editIsActive" class="rounded-sm border-hairline text-primary bg-canvas"/>Aktif</label></div>
                    </div>
                    <div class="flex justify-end pt-2"><button type="submit" class="v-btn-primary text-[13px] px-4 py-1.5">Simpan Perubahan</button></div>
                </form>
            </div>
        </x-modal>

        <div class="v-card overflow-hidden">
            <table class="min-w-full text-sm"><thead><tr class="text-left border-b border-hairline text-ink-subtle text-[12px] uppercase tracking-wider"><th class="py-3 px-4 font-medium">Nama</th><th class="py-3 px-4 font-medium">Email</th><th class="py-3 px-4 font-medium">Peran</th><th class="py-3 px-4 font-medium">Perusahaan</th><th class="py-3 px-4 font-medium">Status</th><th class="py-3 px-4 text-right"></th></tr></thead>
<tbody class="divide-y divide-hairline">
                @foreach($users as $user)
                <tr class="group hover:bg-surface-2/50 transition-colors" wire:key="u-{{ $user->id }}">
                    <td class="py-3 px-4 text-[13px] font-medium text-ink">{{ $user->name }}</td>
                    <td class="py-3 px-4 text-[13px] text-ink-subtle">{{ $user->email }}</td>
                    <td class="py-3 px-4"><span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $user->role === 'superadmin' ? 'bg-primary/15 text-primary' : 'bg-surface-3 text-ink-subtle' }}">{{ ucfirst($user->role) }}</span></td>
                    <td class="py-3 px-4 text-[13px] text-ink-subtle">{{ $user->tenant?->company_name ?? '-' }}</td>

                    <td class="py-3 px-4">
                        @if($user->approval_status === 'pending')
                            <span class="text-[11px] text-yellow-500">● Menunggu</span>
                        @elseif($user->approval_status === 'rejected')
                            <span class="text-[11px] text-red-500">● Ditolak</span>
                        @else
                            @if($user->is_active)
                                <span class="text-[11px] text-green-400">● Aktif</span>
                            @else
                                <span class="text-[11px] text-ink-muted">● Nonaktif</span>
                            @endif
                        @endif
                    </td>

                    <td class="py-3 px-4">
                        <div class="flex gap-1 justify-end opacity-0 group-hover:opacity-100 transition-opacity">

                            @if($user->approval_status === 'pending')
                                <button type="button" wire:click="approveUser({{ $user->id }})" class="p-1.5 text-ink-subtle hover:text-green-400 hover:bg-surface-3 rounded" title="Setujui">
                                    <x-heroicon-o-check class="w-4 h-4" />
                                </button>
                                <button type="button" wire:click="rejectUser({{ $user->id }})" class="p-1.5 text-ink-subtle hover:text-red-400 hover:bg-surface-3 rounded" title="Tolak">
                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                </button>
                                <div class="w-px h-4 bg-hairline my-auto mx-1"></div>
                            @endif

                            <button type="button" wire:click="startEdit({{ $user->id }})" class="p-1.5 text-ink-subtle hover:text-ink hover:bg-surface-3 rounded" title="Edit">
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                            </button>
                            <button type="button" x-data @click="$dispatch('open-confirm-modal', { id: 'delete-user-{{ $user->id }}' })" class="p-1.5 text-ink-subtle hover:text-red-400 hover:bg-surface-3 rounded" title="Hapus">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </div>
                        <x-confirm-modal id="delete-user-{{ $user->id }}" wireClick="deleteUser({{ $user->id }})" title="Hapus Pengguna" message="Apakah Anda yakin ingin menghapus {{ $user->name }}?" confirmText="Hapus" />
                    </td>
                </tr>
                @endforeach
            </tbody></table>
            <div class="p-4">{{ $users->links() }}</div>
        </div>
    </div>
</div>
