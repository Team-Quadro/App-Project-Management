<div>
    <x-slot name="header">
        <div class="flex items-center gap-1.5 text-[13px]">
            <a href="{{ route('projects.index') }}" class="text-ink-subtle hover:text-ink transition-colors duration-100" wire:navigate>Proyek</a>
            <span class="text-hairline-strong">/</span>
            <a href="{{ route('projects.show', $project) }}" class="text-ink-subtle hover:text-ink transition-colors duration-100" wire:navigate>{{ $project->title }}</a>
            <span class="text-hairline-strong">/</span>
            <span class="font-medium text-ink">Edit</span>
        </div>
    </x-slot>

    <div class="w-full">
        <div class="v-card p-5">
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label for="title" class="v-label">Judul <span class="text-red-500">*</span></label>
                    <input type="text" id="title" wire:model="title" required autofocus class="v-input" />
                    @error('title') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="description" class="v-label">Deskripsi</label>
                    <textarea id="description" wire:model="description" rows="3" class="v-input resize-none"></textarea>
                    @error('description') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="stage" class="v-label">Stage Proyek</label>
                        <div x-data="{ open: false }" class="relative w-full">
                            <button type="button" @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-[13px] border transition-colors duration-100 bg-surface-1 border-hairline text-ink hover:border-hairline-strong focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary-focus/50">
                                <span class="truncate">{{ \App\Models\Project::STAGES[$stage] ?? 'Pilih Stage' }}</span>
                                <svg class="w-4 h-4 shrink-0 transition-transform duration-150 text-ink-subtle" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 mt-1 w-full bg-surface-1 border border-hairline rounded-lg shadow-xl z-50 py-1" x-cloak>
                                @foreach (\App\Models\Project::STAGES as $key => $label)
                                <button type="button" wire:click="$set('stage', '{{ $key }}')" @click="open = false"
                                    class="w-full flex items-center justify-between px-3 py-2 text-[13px] text-left transition-colors {{ $stage === $key ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                                    {{ $label }}
                                    @if($stage === $key)
                                    <svg class="w-3 h-3 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="deadline" class="v-label">Tenggat Waktu</label>
                        <input type="date" id="deadline" wire:model="deadline" class="v-input" />
                    </div>
                </div>

                {{-- Team Members --}}
                <div x-data="{ search: '' }">
                    <label class="v-label">Anggota Tim</label>
                    <div class="border border-hairline rounded-md bg-surface-1 overflow-hidden">
                        @if($tenantUsers->count() > 5)
                        <div class="px-3 pt-3">
                            <input type="text" x-model="search" placeholder="Cari anggota..." class="v-input !py-1.5 text-[13px] w-full bg-canvas border-hairline" />
                        </div>
                        @endif
                        <div class="max-h-48 overflow-y-auto sidebar-scroll p-3 space-y-1.5">
                            @forelse ($tenantUsers as $u)
                            <label class="flex items-center gap-3 py-1.5 px-2 rounded-md hover:bg-surface-2 cursor-pointer transition-colors text-[13px]"
                                   x-show="!search || '{{ strtolower($u->name . ' ' . $u->email) }}'.includes(search.toLowerCase())">
                                <input type="checkbox" wire:model="member_ids" value="{{ $u->id }}" class="rounded border-hairline bg-canvas text-primary focus:ring-primary/50 w-4 h-4" />
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <div class="w-6 h-6 rounded-full bg-surface-3 flex items-center justify-center text-[10px] font-bold text-ink-muted shrink-0">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                                    <div class="min-w-0">
                                        <p class="text-ink font-medium truncate">{{ $u->name }}</p>
                                        <p class="text-ink-muted text-[11px] truncate">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </label>
                            @empty
                            <p class="text-[13px] text-ink-subtle text-center py-3">Belum ada anggota tim.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-hairline">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="v-btn-primary text-[13px]" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">Perbarui Proyek</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                        <a href="{{ route('projects.show', $project) }}" class="v-btn-secondary text-[13px]" wire:navigate>Batal</a>
                    </div>
                    <button type="button" x-data @click="$dispatch('open-confirm-modal', { id: 'delete-project' })" class="text-[12px] text-red-400 hover:text-red-300 transition-colors">Hapus Proyek</button>
                    <x-confirm-modal id="delete-project" wireClick="deleteProject" title="Hapus Proyek" message="Anda yakin ingin menghapus proyek ini? Tindakan ini tidak dapat dibatalkan." confirmText="Hapus Proyek" />
                </div>
            </form>
        </div>
    </div>
</div>
