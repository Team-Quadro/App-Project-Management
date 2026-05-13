@section('title', $project->title)

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-[13px]">
            <a href="{{ route('projects.index') }}" class="text-ink-subtle hover:text-ink">Projects</a>
            <span class="text-hairline-strong">/</span>
            <span class="font-medium text-ink">{{ $project->title }}</span>
        </div>
    </x-slot>

    <div class="flex flex-col bg-canvas relative"
         x-data="{
            addingSection: false,
            addingTaskGroup: null,
            editingTaskId: null,
            selectedTaskId: null,
            closeSidebar() { this.selectedTaskId = null; }
         }">

        {{-- Project Header --}}
        <div class="px-6 py-5 border-b border-hairline">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-semibold text-ink tracking-tight">{{ $project->title }}</h1>
                        <x-badge :variant="$project->status">{{ ucfirst(str_replace('_',' ',$project->status)) }}</x-badge>
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
                            {{ $project->deadline->format('M j, Y') }}
                        </div>
                        @endif
                    </div>
                </div>
                @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}" class="p-1.5 text-ink-subtle hover:text-ink hover:bg-surface-2 rounded-md transition-colors" title="Edit Project">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </a>
                @endcan
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="px-6 py-3 flex items-center gap-3 border-b border-hairline bg-surface-1/20">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <form method="GET" action="{{ route('projects.show', $project) }}" class="m-0 w-full">
                    <input type="text" name="search" value="{{ request('search') }}" class="v-input !py-1.5 pl-9 bg-surface-1 border-transparent hover:border-hairline focus:border-primary w-full text-[13px] transition-colors" placeholder="Search tasks...">
                </form>
            </div>
            <form method="GET" action="{{ route('projects.show', $project) }}" class="m-0">
                <select name="task_status" onchange="this.form.submit()" class="v-select !py-1.5 bg-surface-1 text-[13px] border-transparent hover:border-hairline w-32 transition-colors">
                    <option value="">All Status</option>
                    @foreach ($workflowStages as $ws)
                    <option value="{{ $ws->key }}" {{ request('task_status')===$ws->key ? 'selected' : '' }}>{{ $ws->name }}</option>
                    @endforeach
                </select>
            </form>
            @can('update', $project)
            <button @click="addingTaskGroup = addingTaskGroup === '__top__' ? null : '__top__'; setTimeout(() => $refs.quickAddInput?.focus(), 50)" class="v-btn-primary text-[13px] px-3 py-1.5 gap-1.5 shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Task
            </button>
            @endcan
        </div>

        {{-- Task List --}}
        <div class="flex-1 overflow-y-auto pb-20">
            {{-- Table Header --}}
            <div class="grid grid-cols-[1fr_140px_100px_120px_90px_70px] gap-3 px-6 py-2 border-b border-hairline text-[11px] font-medium text-ink-subtle uppercase tracking-wider select-none sticky top-0 bg-canvas z-10">
                <div class="pl-7">Task name</div>
                <div>Assignee</div>
                <div>Due date</div>
                <div>Priority</div>
                <div>Status</div>
                <div></div>
            </div>

            {{-- Quick Add Task (from toolbar button) --}}
            @can('update', $project)
            <div x-show="addingTaskGroup === '__top__'" x-cloak class="px-4 py-3 border-b border-hairline bg-surface-1/30">
                <form method="POST" action="{{ route('projects.tasks.store', $project) }}" class="grid grid-cols-[1fr_140px_100px_120px_90px_70px] gap-3 items-center m-0">
                    @csrf
                    <input type="hidden" name="priority" value="medium">
                    <input type="hidden" name="status" value="{{ $workflowStages->first()?->key ?? 'todo' }}">
                    <input type="hidden" name="stage_id" value="{{ $workflowStages->first()?->id }}">
                    <div class="pl-7">
                        <input type="text" name="title" x-ref="quickAddInput" placeholder="Task title..." class="v-input !py-1.5 !px-2 text-[13px] w-full" @keydown.escape="addingTaskGroup = null" required>
                    </div>
                    <select name="assigned_to" class="v-select !py-1.5 text-[13px] bg-canvas border-hairline">
                        <option value="">Assignee</option>
                        @foreach ($projectUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="deadline" class="v-input !py-1.5 text-[13px] bg-canvas border-hairline text-ink-subtle">
                    <div></div>
                    <button type="submit" class="v-btn-primary !py-1.5 text-[12px]">Add</button>
                    <button type="button" @click="addingTaskGroup = null" class="p-1 text-ink-subtle hover:text-ink justify-self-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </form>
            </div>
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
                @include('projects._task-row', ['task' => $task, 'project' => $project, 'workflowStages' => $workflowStages])
                @endforeach
            </div>
            @endif

            {{-- Grouped by section --}}
            <div id="sections-container">
            @foreach ($groupedTasks as $key => $group)
            @php $stage = $group['stage']; $stageTasks = $group['tasks']; @endphp
            <div class="section-block mt-4 px-4" x-data="{ open: true }" data-section-id="{{ $stage->id }}">
                <div class="flex items-center gap-1 group/sec mb-1 py-1">
                    {{-- Drag grip --}}
                    <div class="section-grip cursor-grab active:cursor-grabbing p-1 text-ink-muted opacity-0 group-hover/sec:opacity-100 transition-opacity shrink-0" title="Drag to reorder">
                        <svg class="w-3.5 h-4" viewBox="0 0 16 20" fill="currentColor"><circle cx="5" cy="4" r="1.5"/><circle cx="11" cy="4" r="1.5"/><circle cx="5" cy="10" r="1.5"/><circle cx="11" cy="10" r="1.5"/><circle cx="5" cy="16" r="1.5"/><circle cx="11" cy="16" r="1.5"/></svg>
                    </div>
                    <button @click="open = !open" class="flex items-center gap-2 flex-1 text-left">
                        <svg class="w-3.5 h-3.5 text-ink-subtle transition-transform duration-150" :class="{'rotate-[-90deg]': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        <span class="inline-block w-2.5 h-2.5 rounded-sm shrink-0" style="background-color: {{ $stage->color ?? '#6b7280' }}"></span>
                        <span class="text-[13px] font-semibold text-ink">{{ $stage->name }}</span>
                        <span class="text-[11px] text-ink-muted ml-1 tabular-nums">{{ $stageTasks->count() }}</span>
                    </button>
                    {{-- Delete section --}}
                    @can('update', $project)
                    <form method="POST" action="{{ route('workflow-stages.destroy', $stage) }}" onsubmit="return confirm('Delete section \'{{ $stage->name }}\'? Tasks will be moved to the first remaining section.')" class="opacity-0 group-hover/sec:opacity-100 transition-opacity">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1 text-ink-muted hover:text-red-400 rounded transition-colors" title="Delete section">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                    @endcan
                </div>

                <div x-show="open" x-collapse class="task-drop-zone border-b border-hairline/30 min-h-[8px]" data-stage-id="{{ $stage->id }}">
                    @foreach ($stageTasks as $task)
                    @include('projects._task-row', ['task' => $task, 'project' => $project, 'workflowStages' => $workflowStages])
                    @endforeach

                    {{-- Inline Add Task --}}
                    @can('update', $project)
                    <div x-show="addingTaskGroup !== '{{ $stage->key }}'"
                         @click="addingTaskGroup = '{{ $stage->key }}'; setTimeout(() => $refs['addInput_{{ $stage->key }}']?.focus(), 50)"
                         class="flex items-center gap-3 py-2 pl-7 text-[13px] text-ink-muted hover:text-ink-subtle cursor-pointer transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add task...
                    </div>
                    <div x-show="addingTaskGroup === '{{ $stage->key }}'" style="display:none" class="py-2">
                        <form method="POST" action="{{ route('projects.tasks.store', $project) }}" class="grid grid-cols-[1fr_140px_100px_120px_90px_70px] gap-3 items-center m-0">
                            @csrf
                            <input type="hidden" name="priority" value="medium">
                            <input type="hidden" name="status" value="{{ $stage->key }}">
                            <input type="hidden" name="stage_id" value="{{ $stage->id }}">
                            <div class="pl-7">
                                <input type="text" name="title" x-ref="addInput_{{ $stage->key }}" placeholder="Task title..." class="v-input !py-1 !px-2 text-[13px] w-full" @keydown.escape="addingTaskGroup = null" required>
                            </div>
                            <select name="assigned_to" class="v-select !py-1 text-[13px] bg-canvas border-hairline">
                                <option value="">Assignee</option>
                                @foreach ($projectUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="deadline" class="v-input !py-1 text-[13px] bg-canvas border-hairline text-ink-subtle">
                            <div></div>
                            <button type="submit" class="v-btn-primary !py-1 text-[12px]">Add</button>
                            <button type="button" @click="addingTaskGroup = null" class="p-1 text-ink-subtle hover:text-ink justify-self-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
            @endforeach
            </div>

            {{-- Add Section --}}
            @can('update', $project)
            <div class="px-6 mt-8">
                <div x-show="!addingSection">
                    <button @click="addingSection = true; setTimeout(() => $refs.sectionNameInput?.focus(), 50)"
                            class="flex items-center gap-2 text-[13px] text-ink-muted hover:text-ink-subtle transition-colors py-2 px-3 rounded-md hover:bg-surface-1 border border-dashed border-hairline w-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Section
                    </button>
                </div>
                <div x-show="addingSection" style="display:none" class="v-card p-4 mt-2">
                    <form method="POST" action="{{ route('workflow-stages.store') }}" class="m-0 space-y-3">
                        @csrf
                        <div class="text-[13px] font-medium text-ink mb-2">New Section</div>
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <label class="v-label">Section Name</label>
                                <input type="text" name="name" x-ref="sectionNameInput" placeholder="e.g. Backlog, Review, Blocked..." class="v-input text-[13px]" required>
                            </div>
                            <div>
                                <label class="v-label">Color</label>
                                <div class="flex items-center gap-2 flex-wrap mt-1">
                                    @foreach(['#6366f1','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#6b7280'] as $c)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="color" value="{{ $c }}" class="sr-only peer" {{ $c === '#6366f1' ? 'checked' : '' }}>
                                        <span class="w-5 h-5 rounded-full block ring-2 ring-transparent peer-checked:ring-white peer-checked:ring-offset-1 peer-checked:ring-offset-canvas transition-all" style="background-color: {{ $c }}"></span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 pt-2 border-t border-hairline">
                            <button type="submit" class="v-btn-primary text-[13px]">Create Section</button>
                            <button type="button" @click="addingSection = false" class="v-btn-secondary text-[13px]">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            @endcan
        </div>

        {{-- Floating Task Detail Sidebar --}}
        <div x-show="selectedTaskId" x-cloak
             class="fixed inset-y-0 right-0 z-50 w-full md:w-[440px] bg-surface-1 border-l border-hairline shadow-2xl flex flex-col"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">

            <div class="flex items-center justify-between px-5 py-4 border-b border-hairline shrink-0">
                <span class="text-[13px] font-medium text-ink-subtle">Task Details</span>
                <button @click="closeSidebar()" class="p-1.5 text-ink-subtle hover:text-ink hover:bg-surface-2 rounded transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                @foreach ($tasks as $task)
                <div x-show="selectedTaskId === {{ $task->id }}" style="display:none" class="h-full">
                    <form method="POST" action="{{ route('projects.tasks.update', [$project, $task]) }}" class="h-full flex flex-col">
                        @csrf
                        @method('PATCH')
                        <div class="p-5 border-b border-hairline">
                            <input type="text" name="title" value="{{ $task->title }}" class="w-full bg-transparent border-transparent hover:border-hairline focus:border-primary text-xl font-semibold text-ink rounded px-2 py-1 outline-none transition-colors placeholder-ink-muted" placeholder="Task title">
                        </div>
                        <div class="p-5 space-y-4 flex-1">
                            <div class="grid grid-cols-[110px_1fr] gap-y-4 gap-x-3 items-center text-[13px]">
                                <div class="text-ink-subtle flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Assignee
                                </div>
                                <select name="assigned_to" class="v-select !py-1 bg-canvas border-hairline text-[13px]">
                                    <option value="">Unassigned</option>
                                    @foreach ($projectUsers as $u)
                                    <option value="{{ $u->id }}" {{ $task->assigned_to == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </select>

                                <div class="text-ink-subtle flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Due date
                                </div>
                                <input type="date" name="deadline" value="{{ $task->deadline?->format('Y-m-d') }}" class="v-input !py-1 bg-canvas border-hairline text-[13px] text-ink">

                                <div class="text-ink-subtle flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Status
                                </div>
                                <select name="status" class="v-select !py-1 bg-canvas border-hairline text-[13px]">
                                    @foreach ($workflowStages as $ws)
                                    <option value="{{ $ws->key }}" {{ $task->status === $ws->key ? 'selected' : '' }}>{{ $ws->name }}</option>
                                    @endforeach
                                </select>

                                <div class="text-ink-subtle flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Priority
                                </div>
                                <select name="priority" class="v-select !py-1 bg-canvas border-hairline text-[13px]">
                                    @foreach (\App\Models\Task::PRIORITIES as $p)
                                    <option value="{{ $p }}" {{ $task->priority === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="pt-4 border-t border-hairline">
                                <label class="v-label mb-1.5">Description</label>
                                <textarea name="description" rows="5" class="v-input w-full bg-canvas border-hairline text-[13px] resize-none" placeholder="Add a description...">{{ $task->description }}</textarea>
                            </div>
                        </div>
                        <div class="p-5 border-t border-hairline shrink-0 flex gap-2 justify-end">
                            <button type="submit" class="v-btn-primary text-[13px]">Save Changes</button>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        <div x-show="selectedTaskId" x-cloak @click="closeSidebar()" class="fixed inset-0 bg-black/20 z-40" x-transition.opacity></div>
    </div>

    {{-- Drag and Drop Script --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        let draggedTask = null;
        let draggedSection = null;

        // === TASK DRAG ===
        // Use event delegation on the document for task drag so it works inside sections
        document.addEventListener('dragstart', (e) => {
            const row = e.target.closest('.task-row');
            if (!row) return; // Not a task row — let other handlers deal with it
            if (draggedSection) { e.preventDefault(); return; }
            e.stopPropagation(); // Prevent section-block from intercepting
            draggedTask = row;
            row.style.opacity = '0.4';
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('application/task-id', row.dataset.taskId);
        });

        document.addEventListener('dragend', (e) => {
            const row = e.target.closest('.task-row');
            if (!row || !draggedTask) return;
            draggedTask.style.opacity = '1';
            draggedTask = null;
            document.querySelectorAll('.task-drop-zone').forEach(z => z.classList.remove('ring-2', 'ring-primary/50', 'bg-primary/5'));
        });

        document.querySelectorAll('.task-drop-zone').forEach(zone => {
            zone.addEventListener('dragover', (e) => {
                if (!draggedTask) return;
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                zone.classList.add('ring-2', 'ring-primary/50', 'bg-primary/5');
            });
            zone.addEventListener('dragleave', () => zone.classList.remove('ring-2', 'ring-primary/50', 'bg-primary/5'));
            zone.addEventListener('drop', (e) => {
                if (!draggedTask) return;
                e.preventDefault();
                zone.classList.remove('ring-2', 'ring-primary/50', 'bg-primary/5');
                const taskId = e.dataTransfer.getData('application/task-id');
                const newStageId = zone.dataset.stageId;
                const projectId = draggedTask.dataset.projectId;
                if (!taskId || !newStageId || !projectId || newStageId === draggedTask.dataset.stageId) return;

                fetch(`/projects/${projectId}/tasks/${taskId}/move`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ stage_id: parseInt(newStageId) }),
                }).then(r => r.json()).then(d => { if (d.success) window.location.reload(); });
            });
        });

        // === SECTION DRAG (via grip only) ===
        const container = document.getElementById('sections-container');
        if (!container) return;

        // Grip mousedown enables draggable on parent section-block
        document.querySelectorAll('.section-grip').forEach(grip => {
            grip.addEventListener('mousedown', () => {
                const block = grip.closest('.section-block');
                if (block) block.setAttribute('draggable', 'true');
            });
        });
        document.addEventListener('mouseup', () => {
            document.querySelectorAll('.section-block[draggable]').forEach(b => b.removeAttribute('draggable'));
        });

        document.querySelectorAll('.section-block').forEach(block => {
            block.addEventListener('dragstart', (e) => {
                // If the drag originated from a task-row, don't interfere
                if (e.target.closest('.task-row')) return;
                // Only allow section drag if draggable was set by grip mousedown
                if (!block.hasAttribute('draggable')) { e.preventDefault(); return; }
                draggedSection = block;
                block.style.opacity = '0.35';
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('application/section-id', block.dataset.sectionId);
            });
            block.addEventListener('dragend', (e) => {
                if (e.target.closest('.task-row')) return;
                if (draggedSection) { draggedSection.style.opacity = '1'; draggedSection.removeAttribute('draggable'); }
                draggedSection = null;
                document.querySelectorAll('.section-block').forEach(b => b.classList.remove('border-t-2', 'border-t-primary'));
            });
            block.addEventListener('dragover', (e) => {
                if (!draggedSection || draggedSection === block) return;
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                block.classList.add('border-t-2', 'border-t-primary');
            });
            block.addEventListener('dragleave', () => block.classList.remove('border-t-2', 'border-t-primary'));
            block.addEventListener('drop', (e) => {
                if (!draggedSection || draggedSection === block) return;
                e.preventDefault();
                block.classList.remove('border-t-2', 'border-t-primary');
                container.insertBefore(draggedSection, block);

                const order = [...container.querySelectorAll('.section-block')].map(b => parseInt(b.dataset.sectionId));
                fetch('/workflow-stages/reorder', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ order }),
                }).then(r => r.json()).then(d => { if (d.success) window.location.reload(); });
            });
        });
    });
    </script>
</x-app-layout>