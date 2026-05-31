<div>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-ink">Master Data</h1>
    </x-slot>

    <div class="space-y-4">
        @if (session()->has('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif
        @if (session()->has('error'))
            <x-alert type="error">{{ session('error') }}</x-alert>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="v-card p-5 relative">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-[15px] font-semibold text-ink">Stage Proyek</h2>
                        <p class="text-[12px] text-ink-muted">Kelola urutan dan status stage proyek untuk perusahaan Anda.</p>
                    </div>
                </div>

                <form wire:submit="addStage" class="mt-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="sm:col-span-2">
                        <label class="v-label">Nama Stage</label>
                        <input type="text" wire:model="stageName" class="v-input text-[13px]" placeholder="Contoh: Kickoff" required>
                        @error('stageName') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="v-label">Urutan</label>
                        <input type="number" min="1" wire:model="stageSortOrder" class="v-input text-[13px]" placeholder="1">
                        @error('stageSortOrder') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="v-label">Status</label>
                        <select wire:model="stageIsActive" class="v-select text-[13px]">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    <div class="sm:col-span-4">
                        <button type="submit" class="v-btn-primary text-[13px]" wire:loading.attr="disabled">Tambah Stage</button>
                    </div>
                </form>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-hairline text-[11px] font-medium text-ink-subtle uppercase tracking-wider">
                                <th class="px-3 py-2">Stage</th>
                                <th class="px-3 py-2">Urutan</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 w-28"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline/50">
                            @forelse ($projectStages as $stage)
                                @if($editingStageId === $stage->id)
                                    <tr class="bg-surface-2/40">
                                        <td class="px-3 py-2">
                                            <input type="text" wire:model="editingStageName" class="v-input text-[13px]" />
                                            @error('editingStageName') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                            @if($stage->is_default)
                                                <p class="text-[10px] text-ink-muted mt-1">Stage default</p>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" min="1" wire:model="editingStageSortOrder" class="v-input text-[13px]" />
                                            @error('editingStageSortOrder') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="px-3 py-2">
                                            <select wire:model="editingStageIsActive" class="v-select text-[13px]">
                                                <option value="1">Aktif</option>
                                                <option value="0">Nonaktif</option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex gap-2">
                                                <button type="button" wire:click="updateStage" class="v-btn-primary text-[12px]">Simpan</button>
                                                <button type="button" wire:click="cancelEditStage" class="v-btn-secondary text-[12px]">Batal</button>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="hover:bg-surface-1/50 transition-colors">
                                        <td class="px-3 py-2">
                                            <div class="text-[13px] text-ink font-medium">{{ $stage->name }}</div>
                                            <div class="text-[11px] text-ink-muted">{{ $stage->key }}</div>
                                        </td>
                                        <td class="px-3 py-2 text-[13px] text-ink-subtle">{{ $stage->sort_order }}</td>
                                        <td class="px-3 py-2">
                                            <span class="text-[11px] px-2 py-0.5 rounded-full {{ $stage->is_active ? 'bg-emerald-500/15 text-emerald-400' : 'bg-surface-3 text-ink-muted' }}">
                                                {{ $stage->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div class="flex gap-1 justify-end">
                                                <button type="button" wire:click="toggleStage({{ $stage->id }})" class="p-1.5 rounded-md hover:bg-surface-2 text-ink-subtle hover:text-ink" title="Toggle status">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                                <button type="button" wire:click="startEditStage({{ $stage->id }})" class="p-1.5 rounded-md hover:bg-surface-2 text-ink-subtle hover:text-ink" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                                </button>
                                                <button type="button" x-data @click="$dispatch('open-confirm-modal', { id: 'delete-stage-{{ $stage->id }}' })" class="p-1.5 rounded-md hover:bg-red-500/10 text-ink-subtle hover:text-red-500" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                                <x-confirm-modal id="delete-stage-{{ $stage->id }}" wireClick="deleteStage({{ $stage->id }})" title="Hapus Stage" message="Anda yakin ingin menghapus stage {{ $stage->name }}?" confirmText="Hapus" />
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-center text-[13px] text-ink-subtle">Belum ada stage.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div wire:loading class="absolute inset-0 bg-surface-1/70 flex items-center justify-center text-[13px] text-ink">
                    Memuat data stage...
                </div>
            </div>

            <div class="v-card p-5 relative">
                <div>
                    <h2 class="text-[15px] font-semibold text-ink">Role User</h2>
                    <p class="text-[12px] text-ink-muted">Kelola daftar jabatan/role untuk anggota tim.</p>
                </div>

                <form wire:submit="addRole" class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="v-label">Nama Role</label>
                        <input type="text" wire:model="roleName" class="v-input text-[13px]" placeholder="Contoh: QA Engineer" required>
                        @error('roleName') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="v-btn-primary text-[13px]" wire:loading.attr="disabled">Tambah Role</button>
                    </div>
                </form>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-hairline text-[11px] font-medium text-ink-subtle uppercase tracking-wider">
                                <th class="px-3 py-2">Role</th>
                                <th class="px-3 py-2">Tipe</th>
                                <th class="px-3 py-2 w-24"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline/50">
                            @forelse ($companyRoles as $role)
                                @if($editingRoleId === $role->id)
                                    <tr class="bg-surface-2/40">
                                        <td class="px-3 py-2">
                                            <input type="text" wire:model="editingRoleName" class="v-input text-[13px]" />
                                            @error('editingRoleName') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="px-3 py-2 text-[12px] text-ink-muted">Custom</td>
                                        <td class="px-3 py-2">
                                            <div class="flex gap-2">
                                                <button type="button" wire:click="updateRole" class="v-btn-primary text-[12px]">Simpan</button>
                                                <button type="button" wire:click="cancelEditRole" class="v-btn-secondary text-[12px]">Batal</button>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    <tr class="hover:bg-surface-1/50 transition-colors">
                                        <td class="px-3 py-2 text-[13px] text-ink font-medium">{{ $role->name }}</td>
                                        <td class="px-3 py-2 text-[12px] text-ink-muted">{{ $role->is_default ? 'Default' : 'Custom' }}</td>
                                        <td class="px-3 py-2">
                                            <div class="flex gap-1 justify-end">
                                                <button type="button" wire:click="startEditRole({{ $role->id }})" class="p-1.5 rounded-md hover:bg-surface-2 text-ink-subtle hover:text-ink" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                                </button>
                                                <button type="button" x-data @click="$dispatch('open-confirm-modal', { id: 'delete-role-{{ $role->id }}' })" class="p-1.5 rounded-md hover:bg-red-500/10 text-ink-subtle hover:text-red-500" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                                <x-confirm-modal id="delete-role-{{ $role->id }}" wireClick="deleteRole({{ $role->id }})" title="Hapus Role" message="Anda yakin ingin menghapus role {{ $role->name }}?" confirmText="Hapus" />
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="3" class="px-3 py-6 text-center text-[13px] text-ink-subtle">Belum ada role.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div wire:loading class="absolute inset-0 bg-surface-1/70 flex items-center justify-center text-[13px] text-ink">
                    Memuat data role...
                </div>
            </div>
        </div>
    </div>
</div>
