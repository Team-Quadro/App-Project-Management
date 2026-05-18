<div>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-[13px]">
            <span class="font-medium text-ink">Tugas Saya</span>
        </div>
    </x-slot>

    <div class="flex flex-col bg-canvas relative">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-hairline">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-ink tracking-tight">Tugas Saya</h1>
                    <p class="text-[14px] text-ink-subtle mt-1">Semua tugas yang ditugaskan kepada Anda.</p>
                </div>
                <div class="flex items-center gap-2 text-[13px] text-ink-subtle">
                    <span class="font-medium text-ink">{{ $tasks->count() }}</span> tugas aktif
                </div>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="px-6 py-3 flex flex-wrap items-center gap-3 border-b border-hairline bg-surface-1/20">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[180px]">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="v-input !py-1.5 pl-9 bg-surface-1 border-transparent hover:border-hairline focus:border-primary w-full text-[13px] transition-colors"
                    placeholder="Cari tugas...">
            </div>

            {{-- Priority filter --}}
            <div x-data="{ open: false }" class="relative shrink-0">
                <button @click="open = !open" class="flex items-center justify-between gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium border transition-colors duration-100
                    {{ $filterPriority ? 'bg-primary/10 border-primary/30 text-primary' : 'bg-surface-2 border-hairline text-ink-subtle hover:text-ink' }}">
                    <span>{{ $filterPriority ? ucfirst($filterPriority) : 'Semua Prioritas' }}</span>
                    <svg class="w-3 h-3 shrink-0 transition-transform duration-150" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="absolute left-0 mt-1 w-40 bg-surface-1 border border-hairline rounded-lg shadow-xl z-50 py-1" x-cloak>
                    <button type="button" wire:click="$set('filterPriority','')" @click="open=false"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] text-left transition-colors {{ !$filterPriority ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                        Semua Prioritas
                        @if(!$filterPriority)<svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@endif
                    </button>
                    @foreach (\App\Models\Task::PRIORITIES as $p)
                    <button type="button" wire:click="$set('filterPriority','{{ $p }}')" @click="open=false"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] text-left transition-colors {{ $filterPriority === $p ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                        {{ ucfirst($p) }}
                        @if($filterPriority === $p)<svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@endif
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Status filter --}}
            <div x-data="{ open: false }" class="relative shrink-0">
                <button @click="open = !open" class="flex items-center justify-between gap-2 px-3 py-1.5 rounded-md text-[13px] font-medium border transition-colors duration-100
                    {{ $filterStatus ? 'bg-primary/10 border-primary/30 text-primary' : 'bg-surface-2 border-hairline text-ink-subtle hover:text-ink' }}">
                    <span>{{ $filterStatus ? ($workflowStages->firstWhere('key', $filterStatus)?->name ?? ucfirst($filterStatus)) : 'Semua Status' }}</span>
                    <svg class="w-3 h-3 shrink-0 transition-transform duration-150" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="absolute left-0 mt-1 w-44 bg-surface-1 border border-hairline rounded-lg shadow-xl z-50 py-1" x-cloak>
                    <button type="button" wire:click="$set('filterStatus','')" @click="open=false"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] text-left transition-colors {{ !$filterStatus ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                        Semua Status
                        @if(!$filterStatus)<svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@endif
                    </button>
                    @foreach ($workflowStages as $ws)
                    <button type="button" wire:click="$set('filterStatus','{{ $ws->key }}')" @click="open=false"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] text-left transition-colors {{ $filterStatus === $ws->key ? 'bg-primary/10 text-primary font-medium' : 'text-ink-subtle hover:bg-surface-2 hover:text-ink' }}">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $ws->color ?? '#6b7280' }}"></span>
                            {{ $ws->name }}
                        </span>
                        @if($filterStatus === $ws->key)<svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>@endif
                    </button>
                    @endforeach
                </div>
            </div>

            @if($search || $filterPriority || $filterStatus)
            <button wire:click="$set('search',''); $set('filterPriority',''); $set('filterStatus','')"
                class="text-[12px] text-ink-subtle hover:text-ink transition-colors px-2 py-1 rounded hover:bg-surface-2">
                Bersihkan Filter
            </button>
            @endif
        </div>

        {{-- Task List --}}
        <div class="flex-1 overflow-y-auto pb-20">
            {{-- Table Header --}}
            <div class="grid grid-cols-[1fr_160px_120px_110px_90px_80px] gap-3 px-6 py-2 border-b border-hairline text-[11px] font-medium text-ink-subtle uppercase tracking-wider select-none sticky top-0 bg-canvas z-10">
                <div class="pl-3">Nama Tugas</div>
                <div>Proyek</div>
                <div>Tenggat Waktu</div>
                <div>Prioritas</div>
                <div>Status</div>
                <div></div>
            </div>

            @if($tasks->count())
                @php
                /** @var \Illuminate\Database\Eloquent\Collection $tasks */
                /** @var \Illuminate\Database\Eloquent\Collection $workflowStages */

                // Group tasks by their stage
                $grouped = collect();
                foreach ($workflowStages as $ws) {
                    $stageTasks = $tasks->where('stage_id', $ws->id)->values();
                    if ($stageTasks->count()) {
                        $grouped[$ws->key] = ['stage' => $ws, 'tasks' => $stageTasks];
                    }
                }
                $ungrouped = $tasks->whereNull('stage_id')->values();
                @endphp

                {{-- Ungrouped tasks --}}
                @if($ungrouped->count())
                <div class="mt-4 px-4">
                    <div class="flex items-center gap-2 mb-1 py-1">
                        <span class="inline-block w-2.5 h-2.5 rounded-sm bg-surface-3 shrink-0"></span>
                        <span class="text-[13px] font-semibold text-ink">Tanpa Bagian</span>
                        <span class="text-[11px] text-ink-muted tabular-nums">{{ $ungrouped->count() }}</span>
                    </div>
                    @foreach ($ungrouped as $task)
                    <div wire:key="ungrouped-{{ $task->id }}" wire:click="selectTask({{ $task->id }})"
                        class="grid grid-cols-[1fr_160px_120px_110px_90px_80px] gap-3 items-center border-b border-hairline/30 py-2.5 pl-3 group/task cursor-pointer hover:bg-surface-1/40 rounded-md transition-colors">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-surface-3 shrink-0"></div>
                            <span class="text-[13px] font-medium text-ink group-hover/task:text-primary transition-colors truncate">{{ $task->title }}</span>
                        </div>
                        <div class="text-[12px] text-ink-subtle truncate">{{ $task->project?->title ?? '-' }}</div>
                        <div class="text-[12px] {{ $task->deadline && $task->deadline->isPast() ? 'text-red-400' : 'text-ink-subtle' }}">
                            {{ $task->deadline ? $task->deadline->format('d M Y') : '-' }}
                        </div>
                        <div>
                            @php
                                $pColors = ['low' => 'bg-blue-500/10 text-blue-400', 'medium' => 'bg-yellow-500/10 text-yellow-400', 'high' => 'bg-red-500/10 text-red-400'];
                            @endphp
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $pColors[$task->priority] ?? 'bg-surface-3 text-ink-subtle' }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                        <div class="text-[12px] text-ink-muted">-</div>
                        <div class="flex justify-end pr-2 opacity-0 group-hover/task:opacity-100 transition-opacity">
                            <svg class="w-4 h-4 text-ink-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Grouped by workflow stage --}}
                @foreach ($grouped as $key => $group)
                @php $stage = $group['stage']; $stageTasks = $group['tasks']; @endphp
                <div class="mt-4 px-4" x-data="{ open: true }">
                    <button @click="open = !open" class="flex items-center gap-2 mb-1 py-1 w-full text-left group/sec">
                        <svg class="w-3.5 h-3.5 text-ink-subtle transition-transform duration-150" :class="{'rotate-[-90deg]': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        <span class="inline-block w-2.5 h-2.5 rounded-sm shrink-0" style="background-color: {{ $stage->color ?? '#6b7280' }}"></span>
                        <span class="text-[13px] font-semibold text-ink">{{ $stage->name }}</span>
                        <span class="text-[11px] text-ink-muted tabular-nums">{{ $stageTasks->count() }}</span>
                    </button>

                    <div x-show="open">
                        @foreach ($stageTasks as $task)
                        <div wire:key="task-{{ $task->id }}" wire:click="selectTask({{ $task->id }})"
                            class="grid grid-cols-[1fr_160px_120px_110px_90px_80px] gap-3 items-center border-b border-hairline/30 py-2.5 pl-3 group/task cursor-pointer hover:bg-surface-1/40 rounded-md transition-colors">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $stage->color ?? '#6b7280' }}"></div>
                                <span class="text-[13px] font-medium text-ink group-hover/task:text-primary transition-colors truncate">{{ $task->title }}</span>
                            </div>
                            <div class="text-[12px] text-ink-subtle truncate">{{ $task->project?->title ?? '-' }}</div>
                            <div class="text-[12px] {{ $task->deadline && $task->deadline->isPast() ? 'text-red-400' : 'text-ink-subtle' }}">
                                {{ $task->deadline ? $task->deadline->format('d M Y') : '-' }}
                            </div>
                            <div>
                                @php
                                    $pColors = ['low' => 'bg-blue-500/10 text-blue-400', 'medium' => 'bg-yellow-500/10 text-yellow-400', 'high' => 'bg-red-500/10 text-red-400'];
                                @endphp
                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-full {{ $pColors[$task->priority] ?? 'bg-surface-3 text-ink-subtle' }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                            <div class="text-[12px] text-ink-subtle">{{ $stage->name }}</div>
                            <div class="flex justify-end pr-2 opacity-0 group-hover/task:opacity-100 transition-opacity">
                                <svg class="w-4 h-4 text-ink-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

            @else
                {{-- Empty state --}}
                <div class="flex flex-col items-center justify-center py-24 text-center px-6">
                    <div class="w-16 h-16 rounded-2xl bg-surface-2 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-ink-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <p class="text-[15px] font-medium text-ink mb-1">Tidak ada tugas ditemukan</p>
                    <p class="text-[13px] text-ink-subtle max-w-xs">
                        @if($search || $filterPriority || $filterStatus)
                            Tidak ada tugas yang cocok dengan filter saat ini. Coba hapus filter.
                        @else
                            Belum ada tugas yang ditugaskan kepada Anda.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Floating Task Detail Sidebar --}}
        @if($selectedTaskId)
        <div class="fixed inset-y-0 right-0 z-50 w-full md:w-[440px] bg-surface-1 border-l border-hairline shadow-2xl flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-hairline shrink-0">
                <div class="flex flex-col">
                    <span class="text-[13px] font-medium text-ink-subtle">Detail Tugas</span>
                    @if($editingTask?->project)
                    <a href="{{ route('projects.show', $editingTask->project) }}" wire:navigate class="text-[11px] text-primary hover:underline mt-0.5">
                        {{ $editingTask->project->title }}
                    </a>
                    @endif
                </div>
                <button wire:click="closeSidebar()" class="p-1.5 text-ink-subtle hover:text-ink hover:bg-surface-2 rounded transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <form wire:submit="updateTask" class="h-full flex flex-col">
                    <div class="p-5 border-b border-hairline">
                        <input type="text" wire:model="editingTaskTitle"
                            class="w-full bg-transparent border-transparent hover:border-hairline focus:border-primary text-xl font-semibold text-ink rounded px-2 py-1 outline-none transition-colors placeholder-ink-muted"
                            placeholder="Judul tugas">
                        @error('editingTaskTitle') <p class="text-xs text-red-400 mt-1 px-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-5 space-y-4 flex-1">
                        <div class="grid grid-cols-[110px_1fr] gap-y-4 gap-x-3 items-center text-[13px]">

                            {{-- Status --}}
                            <div class="text-ink-subtle flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Status
                            </div>
                            <select wire:model="editingTaskStatus" class="v-select !py-1 bg-canvas border-hairline text-[13px]">
                                @foreach ($workflowStages as $ws)
                                <option value="{{ $ws->key }}">{{ $ws->name }}</option>
                                @endforeach
                            </select>

                            {{-- Priority --}}
                            <div class="text-ink-subtle flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Prioritas
                            </div>
                            <select wire:model="editingTaskPriority" class="v-select !py-1 bg-canvas border-hairline text-[13px]">
                                @foreach (\App\Models\Task::PRIORITIES as $p)
                                <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>

                            {{-- Deadline --}}
                            <div class="text-ink-subtle flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Tenggat
                            </div>
                            <input type="date" wire:model="editingTaskDeadline" class="v-input !py-1 bg-canvas border-hairline text-[13px] text-ink">
                        </div>

                        <div class="pt-4 border-t border-hairline">
                            <label class="v-label mb-1.5">Deskripsi</label>
                            <textarea wire:model="editingTaskDescription" rows="5"
                                class="v-input w-full bg-canvas border-hairline text-[13px] resize-none"
                                placeholder="Tambahkan deskripsi..."></textarea>
                        </div>
                    </div>

                    <div class="p-5 border-t border-hairline shrink-0 flex gap-2 justify-end">
                        <button type="button" wire:click="closeSidebar()" class="v-btn-secondary text-[13px]">Batal</button>
                        <button type="submit" class="v-btn-primary text-[13px]">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
        <div wire:click="closeSidebar()" class="fixed inset-0 bg-black/20 z-40"></div>
        @endif
    </div>
</div>
