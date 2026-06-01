<div>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-[13px]">
            <a href="{{ route('projects.index') }}" class="text-ink-subtle hover:text-ink">Proyek</a>
            <span class="text-hairline-strong">/</span>
            <span class="font-medium text-ink">{{ $project->title }}</span>
        </div>
    </x-slot>

    {{-- Asset Flatpickr Lokal (Tanpa CDN) --}}
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/dark.css') }}">
    <script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>

    <div class="flex flex-col bg-canvas relative" x-data="{ addingSection: false }" x-on:section-added.window="addingSection = false">

        {{-- Project Header --}}
        <div class="px-6 py-5 border-b border-hairline">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-semibold text-ink tracking-tight">{{ $project->title }}</h1>
                        @can('update', $project)
                        <div x-data="{ open: false }" class="relative shrink-0">
                            <button @click="open = !open" class="flex items-center gap-1.5 px-2 py-0.5 rounded-[3px] text-[11px] font-[510] border transition-colors duration-100
                                @php
                                    $colors = \App\Models\Project::STAGE_COLORS[$project->stage] ?? ['bg' => 'bg-surface-3', 'text' => 'text-ink-subtle', 'border' => 'border-hairline'];
                                    echo "{$colors['bg']} {$colors['text']} {$colors['border']}";
                                @endphp">
                                <span>{{ $project->stage_label }}</span>
                                <svg class="w-3 h-3 shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 mt-1 w-48 bg-surface-1 border border-hairline rounded-lg shadow-xl z-50 py-1 overflow-hidden" x-cloak>
                                @forelse ($projectStages as $stageOption)
                                <button type="button" wire:click="updateProjectStage('{{ $stageOption->key }}')" @click="open = false" class="w-full flex items-center justify-between px-3 py-2 text-[11px] text-left transition-colors duration-100 {{ $project->stage === $stageOption->key ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                                    <span class="truncate">{{ $stageOption->name }}</span>
                                    @if(! $stageOption->is_active)
                                        <span class="text-[10px] text-ink-muted">Nonaktif</span>
                                    @endif
                                    @if($project->stage === $stageOption->key)
                                    <svg class="w-3 h-3 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </button>
                                @empty
                                <div class="px-3 py-2 text-[11px] text-ink-muted">Belum ada stage.</div>
                                @endforelse
                            </div>
                        </div>
                        @else
                        <x-badge :variant="$project->stage">{{ $project->stage_label }}</x-badge>
                        @endcan
                    </div>
                    @if($project->description)
                        <p class="text-[14px] text-ink-subtle mt-1.5 max-w-3xl">{{ $project->description }}</p>
                    @endif
                    <div class="flex items-center gap-5 mt-3 text-[13px]">
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center text-[9px] font-bold">
                                {{ strtoupper(substr($project->owner->name,0,1)) }}
                            </div>
                            <span class="text-ink-subtle">{{ $project->owner->name }}</span>
                        </div>
                        @if($project->deadline)
                        <div class="flex items-center gap-1.5 {{ $project->deadline->isPast() ? 'text-red-400' : 'text-ink-subtle' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $project->deadline->format('d M Y') }}
                        </div>
                        @endif
                    </div>
                </div>
                @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}" class="p-1.5 text-ink-subtle hover:text-ink hover:bg-surface-2 rounded-md transition-colors" title="Edit Proyek">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </a>
                @endcan
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="px-6 py-3 flex items-center gap-3 border-b border-hairline bg-surface-1/20">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" class="v-input !py-1.5 !pl-10 bg-surface-1 border-transparent hover:border-hairline focus:border-primary w-full text-[13px] transition-colors" placeholder="Cari tugas...">
            </div>
            <div>
                <select wire:model.live="task_status" class="v-select !py-1.5 bg-surface-1 text-[13px] border-transparent hover:border-hairline w-32 transition-colors">
                    <option value="">Semua Status</option>
                    @foreach ($workflowStages as $ws)
                    <option value="{{ $ws->key }}">{{ $ws->name }}</option>
                    @endforeach
                </select>
            </div>
            @can('updateTasks', $project)
            <button wire:click="$set('addingTaskGroup', '__top__')" class="v-btn-primary text-[13px] px-3 py-1.5 gap-1.5 shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Tugas
            </button>
            @endcan
        </div>

        {{-- Task List --}}
        <div class="flex-1 overflow-y-auto pb-20">
            {{-- Table Header --}}
            <div class="grid grid-cols-[1fr_140px_130px_100px_90px_70px] gap-3 px-6 py-2 border-b border-hairline text-[11px] font-medium text-ink-subtle uppercase tracking-wider select-none sticky top-0 bg-canvas z-10">
                <div class="pl-16">Nama tugas</div>
                <div>Ditugaskan</div>
                <div>Tenggat waktu</div>
                <div>Prioritas</div>
                <div>Status</div>
                <div class="text-center">Selesai</div>
            </div>

            {{-- Quick Add Task --}}
            @can('updateTasks', $project)
            @if($addingTaskGroup === '__top__')
            <div class="px-6 py-2 border-b border-hairline bg-surface-1/30">
                <form wire:submit="addTask('{{ $workflowStages->first()?->key ?? 'todo' }}', {{ $workflowStages->first()?->id ?? 'null' }})" class="grid grid-cols-[1fr_140px_130px_100px_90px_70px] gap-3 items-center m-0">
                    <div class="pl-16">
                        <input type="text" wire:model="newTaskTitle" placeholder="Judul tugas..." class="v-input !py-1 !px-2 text-[13px] w-full" autofocus required>
                    </div>
                    <div>
                        <select wire:model="newTaskAssignee" class="v-select !py-1 text-[13px] bg-canvas border-hairline w-full">
                            <option value="">Ditugaskan</option>
                            @foreach ($projectUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Flatpickr Input (Tambah Tugas Atas) --}}
                    <div x-data="{ date: @entangle('newTaskDeadline') }"
                         x-init="const fp = flatpickr($refs.input, {
                             enableTime: true,
                             time_24hr: true,
                             dateFormat: 'Y-m-d H:i',
                             onChange: function(selectedDates, dateStr) { date = dateStr; }
                         });
                         $watch('date', value => { if(!value) fp.clear(); else fp.setDate(value); });">
                        <input type="text" x-ref="input" placeholder="Pilih waktu..." class="v-input !py-1 text-[13px] bg-canvas border-hairline text-ink-subtle w-full cursor-pointer">
                    </div>

                    <div>
                        <select wire:model="newTaskPriority" class="v-select !py-1 text-[13px] bg-canvas border-hairline w-full">
                            @foreach (\App\Models\Task::PRIORITIES as $p)
                            <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div></div>
                    <div class="flex items-center justify-center gap-1">
                        <button type="submit" class="v-btn-primary !py-1 !px-2 text-[12px]">Tambah</button>
                        <button type="button" wire:click="$set('addingTaskGroup', null)" class="p-1 text-ink-subtle hover:text-ink shrink-0">
                            <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </form>
            </div>
            @endif
            @endcan

            @php
                $groupedTasks = $workflowStages->mapWithKeys(fn($s) => [
                    $s->key => ['stage' => $s, 'tasks' => $tasks->where('stage_id', $s->id)->values()]
                ]);
                $ungrouped = $tasks->whereNull('stage_id')->values();
            @endphp

            {{-- Ungrouped tasks --}}
            @if($ungrouped->count())
            <div class="px-6 divide-y divide-hairline/50 task-drop-zone" data-stage-id="">
                @foreach ($ungrouped as $task)
                        <div class="task-row border-b border-hairline/30 py-3 pl-20 group/task hover:bg-surface-1/30 transition-colors" data-task-id="{{ $task->id }}" draggable="true" wire:key="ungrouped-task-{{ $task->id }}">
                            <div class="grid grid-cols-[1fr_140px_130px_100px_90px_70px] gap-3 items-center">
                                <div class="flex items-center gap-3 cursor-pointer" wire:click="selectTask({{ $task->id }})">
                                    <div class="w-2.5 h-2.5 rounded-full bg-gray-500"></div>
                                    <span class="text-[13px] font-medium text-ink group-hover/task:text-primary transition-colors">{{ $task->title }}</span>
                                </div>
                                <div class="text-[12px] text-ink-subtle truncate">
                                    {{ $task->assignee ? $task->assignee->name : 'Tidak Ditugaskan' }}
                                </div>
                                <div class="text-[12px] {{ $task->deadline && $task->deadline->isPast() ? 'text-red-400 font-medium' : 'text-ink-subtle' }}">
                                    {{ $task->deadline ? $task->deadline->format('d M Y, H:i') . ' WIB' : '-' }}
                                </div>
                                <div>
                                    <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-surface-3 text-ink-subtle">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                                <div class="text-[12px] text-ink-subtle">Tanpa Grup</div>
                                <div class="flex items-center justify-center">
                                    <input type="checkbox"
                                           wire:click="toggleTaskDone({{ $task->id }})"
                                           class="w-4 h-4 text-green-500 bg-surface-1 border-hairline rounded focus:ring-green-500 cursor-pointer transition-colors"
                                           {{ strtolower($task->stage->key ?? $task->status) === 'done' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                @endforeach
            </div>
            @endif

            {{-- Grouped by section --}}
            <div id="sections-container">
            @foreach ($groupedTasks as $key => $group)
            @php $stage = $group['stage']; $stageTasks = $group['tasks']; @endphp
            <div class="section-block mt-4 mb-2 px-4" x-data="{ open: true }" data-section-id="{{ $stage->id }}" wire:key="stage-{{ $stage->id }}">

                {{-- HEADER STAGE --}}
                <div class="flex items-center gap-2 group/sec mb-3 py-2 border-b border-hairline/30">
                    <div class="section-grip cursor-grab active:cursor-grabbing p-1 text-ink-muted opacity-0 group-hover/sec:opacity-100 transition-opacity shrink-0" title="Geser untuk mengurutkan">
                        <svg class="w-4 h-4" viewBox="0 0 16 20" fill="currentColor"><circle cx="5" cy="4" r="1.5"/><circle cx="11" cy="4" r="1.5"/><circle cx="5" cy="10" r="1.5"/><circle cx="11" cy="10" r="1.5"/><circle cx="5" cy="16" r="1.5"/><circle cx="11" cy="16" r="1.5"/></svg>
                    </div>
                    <button @click="open = !open" class="flex items-center gap-3 flex-1 text-left">
                        <svg class="w-4 h-4 text-ink-subtle transition-transform duration-150" :class="{'rotate-[-90deg]': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>

                        <div class="flex items-center gap-2">
                            <span class="inline-block w-3 h-3 rounded-full shrink-0 shadow-sm" style="background-color: {{ $stage->color ?? '#6b7280' }}"></span>
                            <h2 class="text-lg font-bold text-ink tracking-tight">{{ $stage->name }}</h2>
                        </div>

                        <span class="text-[13px] font-medium text-ink-muted ml-2 tabular-nums">{{ $stageTasks->count() }}</span>
                    </button>
                    @can('update', $project)
                    <div class="opacity-0 group-hover/sec:opacity-100 transition-opacity">
                        <button type="button" x-data @click="$dispatch('open-confirm-modal', { id: 'delete-stage-{{ $stage->id }}' })" class="p-1.5 text-ink-muted hover:text-red-400 rounded transition-colors" title="Hapus bagian">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                        <x-confirm-modal id="delete-stage-{{ $stage->id }}" wireClick="deleteTaskSection({{ $stage->id }})" title="Hapus Bagian" message="Hapus bagian '{{ $stage->name }}'? Tugas akan ditandai belum dikelompokkan." confirmText="Hapus" />
                    </div>
                    @endcan
                </div>

                <div x-show="open" class="task-drop-zone min-h-[10px]" data-stage-id="{{ $stage->id }}">
                    @foreach ($stageTasks as $task)
                        <div class="task-row border-b border-hairline/30 py-3 pl-22 group/task hover:bg-surface-1/30 transition-colors" data-task-id="{{ $task->id }}" draggable="true" wire:key="task-{{ $task->id }}">
                            <div class="grid grid-cols-[1fr_140px_130px_100px_90px_70px] gap-3 items-center">
                                <div class="flex items-center gap-3 cursor-pointer" wire:click="selectTask({{ $task->id }})">
                                    <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $stage->color ?? '#6b7280' }}"></div>
                                    <span class="text-[13px] font-medium text-ink group-hover/task:text-primary transition-colors">{{ $task->title }}</span>
                                </div>
                                <div class="text-[12px] text-ink-subtle truncate">
                                    {{ $task->assignee ? $task->assignee->name : 'Tidak Ditugaskan' }}
                                </div>
                                <div class="text-[12px] {{ $task->deadline && $task->deadline->isPast() ? 'text-red-400 font-medium' : 'text-ink-subtle' }}">
                                    {{ $task->deadline ? $task->deadline->format('d M Y, H:i') . ' WIB' : '-' }}
                                </div>
                                <div>
                                    <span class="text-[11px] font-medium px-2.5 py-1 rounded-full bg-surface-3 text-ink-subtle">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                                <div class="text-[12px] text-ink-subtle font-medium">{{ $stage->name }}</div>

                                <div class="flex items-center justify-center">
                                    <input type="checkbox"
                                           wire:click="toggleTaskDone({{ $task->id }})"
                                           class="w-4 h-4 text-green-500 bg-surface-1 border-hairline rounded focus:ring-green-500 cursor-pointer transition-colors"
                                           {{ strtolower($task->stage->key ?? $task->status) === 'done' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Inline Add Task --}}
                    @can('updateTasks', $project)
                    @if($addingTaskGroup !== $stage->key)
                    <div wire:click="$set('addingTaskGroup', '{{ $stage->key }}')" class="flex items-center gap-3 py-3 pl-18 text-[13px] text-ink-muted hover:text-ink-subtle cursor-pointer transition-colors mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah tugas...
                    </div>
                    @endif
                    @if($addingTaskGroup === $stage->key)
                    <div class="py-2 pr-6 mt-1 border-y border-hairline/30 bg-surface-1/30">
                        <form wire:submit="addTask('{{ $stage->key }}', {{ $stage->id }})" class="grid grid-cols-[1fr_140px_130px_100px_90px_70px] gap-3 items-center m-0">
                            <div class="pl-16">
                                <input type="text" wire:model="newTaskTitle" placeholder="Judul tugas..." class="v-input !py-1 !px-2 text-[13px] w-full" autofocus required>
                            </div>
                            <div>
                                <select wire:model="newTaskAssignee" class="v-select !py-1 text-[13px] bg-canvas border-hairline w-full">
                                    <option value="">Ditugaskan</option>
                                    @foreach ($projectUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Flatpickr Input --}}
                            <div x-data="{ date: @entangle('newTaskDeadline') }"
                                 x-init="const fp = flatpickr($refs.input, {
                                     enableTime: true,
                                     time_24hr: true,
                                     dateFormat: 'Y-m-d H:i',
                                     onChange: function(selectedDates, dateStr) { date = dateStr; }
                                 });
                                 $watch('date', value => { if(!value) fp.clear(); else fp.setDate(value); });">
                                <input type="text" x-ref="input" placeholder="Pilih waktu..." class="v-input !py-1 text-[13px] bg-canvas border-hairline text-ink-subtle w-full cursor-pointer">
                            </div>

                            <div>
                                <select wire:model="newTaskPriority" class="v-select !py-1 text-[13px] bg-canvas border-hairline w-full">
                                    @foreach (\App\Models\Task::PRIORITIES as $p)
                                    <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div></div>
                            <div class="flex items-center justify-center gap-1">
                                <button type="submit" class="v-btn-primary !py-1 !px-2 text-[12px]">Tambah</button>
                                <button type="button" wire:click="$set('addingTaskGroup', null)" class="p-1 text-ink-subtle hover:text-ink shrink-0">
                                    <x-heroicon-o-x-mark class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                    @endcan
                </div>
            </div>
            @endforeach
            </div>

            {{-- Add Section --}}
            @can('update', $project)
            <div class="px-6 mt-8">
                <div x-show="!addingSection">
                    <button @click="addingSection = true"
                            class="flex items-center gap-2 text-[14px] text-ink-muted hover:text-ink transition-colors py-3 px-4 rounded-lg hover:bg-surface-1 border border-dashed border-hairline w-full shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span class="font-medium">Tambah Bagian Stage Baru</span>
                    </button>
                </div>
                <div x-show="addingSection" style="display:none" class="v-card p-5 mt-2 shadow-lg border border-hairline">
                    <form wire:submit="addSection" class="m-0 space-y-4">
                        <div class="text-[14px] font-bold text-ink mb-1">Bagian Baru</div>
                        <div class="flex items-start gap-4 flex-col md:flex-row">
                            <div class="flex-1 w-full">
                                <label class="v-label">Nama Bagian</label>
                                <input type="text" wire:model="newSectionName" placeholder="Contoh: Backlog, Review, Blocked..." class="v-input text-[13px] w-full mt-1" required>
                            </div>
                            <div class="w-full md:w-auto">
                                <label class="v-label">Warna Label</label>
                                <div class="flex items-center gap-2 flex-wrap mt-2">
                                    @foreach(['#6366f1','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#6b7280'] as $c)
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model="newSectionColor" value="{{ $c }}" class="sr-only peer">
                                        <span class="w-6 h-6 rounded-full block ring-2 ring-transparent peer-checked:ring-white peer-checked:ring-offset-2 peer-checked:ring-offset-canvas transition-all" style="background-color: {{ $c }}"></span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-hairline/50 mt-2">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" wire:model="newSectionIsActive" class="rounded text-primary focus:ring-primary w-4 h-4 border-hairline bg-surface-1">
                                <span class="text-[13px] text-ink-subtle font-medium">Kategorikan sebagai Task Aktif (Muncul di Halaman Global Monitoring)</span>
                            </label>
                        </div>

                        <div class="flex items-center gap-2 pt-4 mt-2">
                            <button type="submit" class="v-btn-primary px-4 text-[13px]">Buat Bagian</button>
                            <button type="button" @click="addingSection = false" class="v-btn-secondary px-4 text-[13px]">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
            @endcan
        </div>

        {{-- Floating Task Detail Sidebar --}}
        @if($selectedTaskId)
        <div class="fixed inset-y-0 right-0 z-50 w-full md:w-[440px] bg-surface-1 border-l border-hairline shadow-2xl flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-hairline shrink-0">
                <span class="text-[13px] font-medium text-ink-subtle">Detail Tugas</span>
                <button wire:click="closeSidebar()" class="p-1.5 text-ink-subtle hover:text-ink hover:bg-surface-2 rounded transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="h-full">
                    <form wire:submit="updateTask" class="h-full flex flex-col">
                        <div class="p-5 border-b border-hairline">
                            <input type="text" wire:model="editingTaskTitle" class="w-full bg-transparent border-transparent hover:border-hairline focus:border-primary text-xl font-semibold text-ink rounded px-2 py-1 outline-none transition-colors placeholder-ink-muted" placeholder="Judul tugas">
                        </div>
                        <div class="p-5 space-y-4 flex-1">
                            <div class="grid grid-cols-[110px_1fr] gap-y-4 gap-x-3 items-center text-[13px]">
                                <div class="text-ink-subtle flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Ditugaskan
                                </div>
                                <select wire:model="editingTaskAssignee" class="v-select !py-1 bg-canvas border-hairline text-[13px]">
                                    <option value="">Tidak Ditugaskan</option>
                                    @foreach ($projectUsers as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>

                                <div class="text-ink-subtle flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Tenggat
                                </div>

                                {{-- Flatpickr Input (Sidebar Kanan) --}}
                                <div x-data="{ deadline: @entangle('editingTaskDeadline') }"
                                     x-init="const fp = flatpickr($refs.input, {
                                         enableTime: true,
                                         time_24hr: true,
                                         dateFormat: 'Y-m-d H:i',
                                         onChange: function(selectedDates, dateStr) { deadline = dateStr; }
                                     });
                                     $watch('deadline', value => { if(value) fp.setDate(value); else fp.clear(); });">
                                    <input type="text" x-ref="input" placeholder="Pilih waktu..." class="v-input !py-1 bg-canvas border-hairline text-[13px] text-ink w-full cursor-pointer">
                                </div>

                                <div class="text-ink-subtle flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Status
                                </div>
                                <select wire:model="editingTaskStatus" class="v-select !py-1 bg-canvas border-hairline text-[13px]">
                                    @foreach ($workflowStages as $ws)
                                    <option value="{{ $ws->key }}">{{ $ws->name }}</option>
                                    @endforeach
                                </select>

                                <div class="text-ink-subtle flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Prioritas
                                </div>
                                <select wire:model="editingTaskPriority" class="v-select !py-1 bg-canvas border-hairline text-[13px]">
                                    @foreach (\App\Models\Task::PRIORITIES as $p)
                                    <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="pt-4 border-t border-hairline">
                                <label class="v-label mb-1.5">Deskripsi</label>
                                <textarea wire:model="editingTaskDescription" rows="5" class="v-input w-full bg-canvas border-hairline text-[13px] resize-none" placeholder="Tambahkan deskripsi..."></textarea>
                            </div>
                        </div>
                        <div class="p-5 border-t border-hairline shrink-0 flex gap-2 justify-end">
                            <button type="submit" class="v-btn-primary text-[13px]">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div wire:click="closeSidebar()" class="fixed inset-0 bg-black/20 z-40" x-transition.opacity></div>
        @endif
    </div>

    {{-- Drag and Drop Script --}}
    @script
    <script>
        let draggedTask = null;
        let draggedSection = null;

        document.addEventListener('dragstart', (e) => {
            const row = e.target.closest('.task-row');
            if (row) {
                if (draggedSection) { e.preventDefault(); return; }
                draggedTask = row;
                row.style.opacity = '0.4';
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', row.dataset.taskId);
                return;
            }
            const block = e.target.closest('.section-block');
            if (block && block.hasAttribute('draggable')) {
                draggedSection = block;
                block.style.opacity = '0.35';
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', block.dataset.sectionId);
            }
        });

        document.addEventListener('dragend', () => {
            if (draggedTask) {
                draggedTask.style.opacity = '1';
                draggedTask = null;
                document.querySelectorAll('.task-drop-zone').forEach(z => z.classList.remove('ring-2', 'ring-primary/50', 'bg-primary/5'));
            }
            if (draggedSection) {
                draggedSection.style.opacity = '1';
                draggedSection.removeAttribute('draggable');
                draggedSection = null;
                document.querySelectorAll('.section-block').forEach(b => b.classList.remove('border-t-2', 'border-t-primary'));
            }
        });

        document.addEventListener('dragover', (e) => {
            if (draggedTask) {
                const zone = e.target.closest('.task-drop-zone');
                if (zone) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    document.querySelectorAll('.task-drop-zone').forEach(z => z.classList.remove('ring-2', 'ring-primary/50', 'bg-primary/5'));
                    zone.classList.add('ring-2', 'ring-primary/50', 'bg-primary/5');
                }
            } else if (draggedSection) {
                const block = e.target.closest('.section-block');
                if (block && draggedSection !== block) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                    document.querySelectorAll('.section-block').forEach(b => b.classList.remove('border-t-2', 'border-t-primary'));
                    block.classList.add('border-t-2', 'border-t-primary');
                }
            }
        });

        document.addEventListener('dragleave', (e) => {
            const zone = e.target.closest('.task-drop-zone');
            if (zone && !zone.contains(e.relatedTarget)) {
                zone.classList.remove('ring-2', 'ring-primary/50', 'bg-primary/5');
            }
            const block = e.target.closest('.section-block');
            if (block && !block.contains(e.relatedTarget)) {
                block.classList.remove('border-t-2', 'border-t-primary');
            }
        });

        document.addEventListener('drop', (e) => {
            if (draggedTask) {
                const zone = e.target.closest('.task-drop-zone');
                if (zone) {
                    e.preventDefault();
                    zone.classList.remove('ring-2', 'ring-primary/50', 'bg-primary/5');
                    const taskId = draggedTask.dataset.taskId;
                    const newStageId = zone.dataset.stageId;
                    const oldStageId = draggedTask.closest('.task-drop-zone')?.dataset?.stageId;
                    if (taskId && newStageId !== undefined && String(newStageId) !== String(oldStageId)) {
                        let parsedStage = newStageId ? parseInt(newStageId) : null;
                        $wire.handleTaskMoved(parseInt(taskId), parsedStage);
                    }
                }
            } else if (draggedSection) {
                const block = e.target.closest('.section-block');
                if (block && draggedSection !== block) {
                    e.preventDefault();
                    block.classList.remove('border-t-2', 'border-t-primary');
                    const container = document.getElementById('sections-container');
                    container.insertBefore(draggedSection, block);
                    const order = [...container.querySelectorAll('.section-block')].map(b => parseInt(b.dataset.sectionId));
                    $wire.handleSectionReordered(order);
                }
            }
        });

        document.addEventListener('mousedown', (e) => {
            const grip = e.target.closest('.section-grip');
            if (grip) {
                const block = grip.closest('.section-block');
                if (block) block.setAttribute('draggable', 'true');
            }
        });
        document.addEventListener('mouseup', () => {
            document.querySelectorAll('.section-block[draggable]').forEach(b => b.removeAttribute('draggable'));
        });
    </script>
    @endscript
</div>
