<div>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h1 class="text-sm font-semibold text-ink">Proyek</h1>
        </div>
    </x-slot>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div class="flex flex-1 items-center gap-2 w-full sm:w-auto">
            <div class="relative w-full max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari proyek..."
                    class="v-input pl-9 bg-canvas" />
            </div>
            <div x-data="{ open: false }" class="relative shrink-0">
                <button @click="open = !open" class="flex items-center justify-between w-48 px-3 py-1.5 rounded-md text-[13px] font-medium border transition-colors duration-100
                    {{ $stage ? 'bg-primary/10 border-primary/30 text-primary' : 'bg-surface-2 border-hairline text-ink-subtle hover:text-ink' }}">
                    <span class="truncate">{{ $stage ? (\App\Models\Project::STAGES[$stage] ?? ucfirst($stage)) : 'Semua Stage' }}</span>
                    <svg class="w-3 h-3 shrink-0 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="absolute left-0 mt-1 w-48 bg-surface-1 border border-hairline rounded-lg shadow-xl z-50 py-1 overflow-hidden" x-cloak>
                    <button type="button" wire:click="$set('stage', '')" @click="open = false" class="w-full flex items-center justify-between px-3 py-2 text-[13px] text-left transition-colors duration-100 {{ !$stage ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                        Semua Stage
                        @if(!$stage)
                        <svg class="w-3 h-3 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                    @foreach (\App\Models\Project::STAGES as $key => $label)
                    <button type="button" wire:click="$set('stage', '{{ $key }}')" @click="open = false" class="w-full flex items-center justify-between px-3 py-2 text-[13px] text-left transition-colors duration-100 {{ $stage === $key ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                        {{ $label }}
                        @if($stage === $key)
                        <svg class="w-3 h-3 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>
            @if ($search || $stage)
            <button wire:click="$set('search', ''); $set('stage', '')" class="text-[13px] text-ink-subtle hover:text-ink transition-colors px-2">Bersihkan</button>
            @endif

            {{-- Superadmin subsidiary switcher inside Project List Filter --}}
            @if (Auth::user()->isSuperAdmin())
            @php
                $allTenants = \App\Models\Tenant::orderBy('company_name')->get();
                $activeTenantId = session('superadmin_tenant_id');
                $activeTenant = $activeTenantId ? $allTenants->firstWhere('id', $activeTenantId) : null;
            @endphp
            <div x-data="{ open: false }" class="relative shrink-0 ml-2">
                <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium border transition-colors duration-100
                    {{ $activeTenant ? 'bg-primary/10 border-primary/30 text-primary' : 'bg-surface-2 border-hairline text-ink-subtle hover:text-ink' }}">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 7.5h15M6 7.5V21m12-13.5V21M7.5 3h9l1.5 4.5H6L7.5 3z"/></svg>
                    <span class="truncate max-w-[160px]">{{ $activeTenant?->company_name ?? 'Semua Anak Perusahaan' }}</span>
                    <svg class="w-3 h-3 shrink-0 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="absolute left-0 mt-1 w-56 bg-surface-1 border border-hairline rounded-lg shadow-xl z-50 py-1 overflow-hidden" x-cloak>
                    <div class="px-3 py-2 border-b border-hairline">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-ink-muted">Pilih Anak Perusahaan</p>
                    </div>
                    <div class="max-h-64 overflow-y-auto sidebar-scroll">
                        <button type="button" wire:click="switchTenant('')" @click="open = false" class="w-full flex items-center gap-2 px-3 py-2 text-[13px] text-left transition-colors duration-100 {{ !$activeTenantId ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            Semua Anak Perusahaan
                            @unless($activeTenantId)
                            <svg class="w-3 h-3 ml-auto text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @endunless
                        </button>

                        @foreach ($allTenants as $t)
                        <button type="button" wire:click="switchTenant('{{ $t->id }}')" @click="open = false" class="w-full flex items-center gap-2 px-3 py-2 text-[13px] text-left transition-colors duration-100 {{ $activeTenantId == $t->id ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                            <span class="w-5 h-5 rounded bg-surface-3 flex items-center justify-center text-[10px] font-bold text-ink-muted shrink-0">{{ strtoupper(substr($t->company_name, 0, 1)) }}</span>
                            <span class="truncate">{{ $t->company_name }}</span>
                            @if($activeTenantId == $t->id)
                            <svg class="w-3 h-3 ml-auto text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @endif
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        @can('create', App\Models\Project::class)
        <a href="{{ route('projects.create') }}" class="v-btn-primary text-[13px] gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Proyek Baru
        </a>
        @endcan
    </div>

    {{-- Project List --}}
    <div class="v-card overflow-hidden">
        @if ($projects->count())
        <div class="overflow-x-auto relative">
            <div wire:loading class="absolute inset-0 bg-surface-1/50 z-10 flex items-center justify-center">
                <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-hairline text-ink-subtle text-[12px] uppercase tracking-wider">
                        <th class="py-3 px-4 font-medium w-1/3">Proyek</th>
                        <th class="py-3 px-4 font-medium">Stage</th>
                        <th class="py-3 px-4 font-medium">Pemilik</th>
                        <th class="py-3 px-4 font-medium">Tugas</th>
                        <th class="py-3 px-4 font-medium">Tenggat Waktu</th>
                        <th class="py-3 px-4 font-medium text-right">Progres</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @foreach ($projects as $project)
                    <tr class="group hover:bg-surface-2/50 transition-colors cursor-pointer" onclick="window.location='{{ route('projects.show', $project) }}'">
                        <td class="py-3 px-4">
                            <div class="flex flex-col">
                                <span class="font-medium text-ink group-hover:text-primary transition-colors text-[14px]">{{ $project->title }}</span>
                                @if ($project->description)
                                <span class="text-ink-subtle text-[12px] truncate max-w-sm mt-0.5">{{ $project->description }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <x-badge :variant="$project->stage" size="xs">{{ $project->stage_label }}</x-badge>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded-full bg-primary text-white text-[9px] font-medium flex items-center justify-center">
                                    {{ strtoupper(substr($project->owner->name, 0, 1)) }}
                                </div>
                                <span class="text-[13px] text-ink-muted">{{ $project->owner->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 tabular-nums text-[13px] text-ink-muted">
                            {{ $project->tasks_count }}
                        </td>
                        <td class="py-3 px-4 text-[13px] text-ink-muted tabular-nums">
                            @if ($project->deadline)
                            <span class="{{ $project->deadline->isPast() ? 'text-red-400' : '' }}">
                                {{ $project->deadline->format('M d, Y') }}
                            </span>
                            @else
                            -
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            @php
                                $totalTasks = $project->tasks_count;
                                $doneCount = 0;
                                foreach($project->tasks as $t) {
                                    if ($t->status === 'done') $doneCount++;
                                }
                                $progress = $totalTasks > 0 ? ($doneCount / $totalTasks) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-end gap-2">
                                <div class="w-16 h-1.5 bg-surface-3 rounded-full overflow-hidden">
                                    <div class="h-full bg-primary rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="text-[12px] text-ink-subtle w-8 text-right tabular-nums">{{ number_format($progress, 0) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-hairline">
            {{ $projects->links(data: ['scrollTo' => false]) }}
        </div>
        @else
        <div class="text-center py-16">
            <p class="text-[14px] font-medium text-ink mb-1">Tidak ada proyek ditemukan</p>
            <p class="text-[13px] text-ink-subtle">Mulai dengan membuat proyek pertama Anda.</p>
            @can('create', App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="v-btn-primary text-[13px] mt-4 gap-1.5 inline-flex">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Proyek Baru
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>
