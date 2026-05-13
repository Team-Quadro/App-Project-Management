{{-- _task-row.blade.php --}}
@php
    $lastStage = $workflowStages->sortByDesc('sort_order')->first();
    $firstStage = $workflowStages->sortBy('sort_order')->first();
    $isCompleted = $lastStage && $task->stage_id === $lastStage->id;
    $toggleStatus = $isCompleted ? ($firstStage?->key ?? 'todo') : ($lastStage?->key ?? 'done');
    $stageForTask = $workflowStages->firstWhere('key', $task->status);
    $stageColor = $stageForTask?->color ?? '#6b7280';

    $priorityConfig = match($task->priority) {
        'high'   => ['color' => '#ef4444', 'icon' => '↑↑', 'label' => 'High'],
        'medium' => ['color' => '#f59e0b', 'icon' => '↑',  'label' => 'Medium'],
        'low'    => ['color' => '#6b7280', 'icon' => '↓',  'label' => 'Low'],
        default  => ['color' => '#6b7280', 'icon' => '—',  'label' => ucfirst($task->priority ?? 'none')],
    };
@endphp
<div class="task-row grid grid-cols-[1fr_140px_100px_120px_90px_70px] gap-3 py-2.5 items-center group/row hover:bg-surface-2/50 transition-colors duration-150 cursor-grab active:cursor-grabbing rounded-md -mx-2 px-2"
     draggable="true"
     data-task-id="{{ $task->id }}"
     data-project-id="{{ $project->id }}"
     data-stage-id="{{ $task->stage_id }}"
     @click="selectedTaskId = {{ $task->id }}">

    {{-- Task Name with check circle --}}
    <div class="flex items-center gap-3 pl-1 min-w-0">
        <form method="POST" action="{{ route('projects.tasks.status', [$project, $task]) }}" class="shrink-0" @click.stop>
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="{{ $toggleStatus }}">
            <button type="submit" class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all duration-150
                {{ $isCompleted
                    ? 'bg-green-500/20 border-green-500 text-green-500'
                    : 'border-ink-muted hover:border-green-500 hover:text-green-500 text-transparent' }}" title="{{ $isCompleted ? 'Mark incomplete' : 'Mark complete' }}">
                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </form>
        <div class="flex-1 min-w-0">
            <span class="text-[13px] font-medium truncate block
                {{ $isCompleted ? 'text-ink-subtle line-through' : 'text-ink' }}">
                {{ $task->title }}
            </span>
        </div>
    </div>

    {{-- Assignee Avatar --}}
    <div class="flex items-center gap-2" @click.stop>
        @if ($task->assignee)
            <div class="w-6 h-6 rounded-full bg-primary/80 text-white flex items-center justify-center text-[10px] font-bold shrink-0" title="{{ $task->assignee->name }}">
                {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
            </div>
            <span class="text-[12px] text-ink-subtle truncate">{{ $task->assignee->name }}</span>
        @else
            <div class="w-6 h-6 rounded-full border-2 border-dashed border-hairline-strong flex items-center justify-center text-ink-muted hover:border-primary hover:text-primary transition-colors cursor-pointer" title="Assign">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        @endif
    </div>

    {{-- Due Date --}}
    <div class="flex items-center gap-1.5 text-[12px]" @click.stop>
        @if ($task->deadline)
            <svg class="w-3 h-3 shrink-0 {{ $task->deadline->isPast() && !$isCompleted ? 'text-red-400' : 'text-ink-subtle' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="{{ $task->deadline->isPast() && !$isCompleted ? 'text-red-400' : 'text-ink-subtle' }}">
                {{ $task->deadline->format('M j') }}
            </span>
        @else
            <span class="text-ink-muted">—</span>
        @endif
    </div>

    {{-- Priority --}}
    <div class="flex items-center" @click.stop>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold"
              style="color: {{ $priorityConfig['color'] }}">
            <span class="text-[10px]">{{ $priorityConfig['icon'] }}</span>
            {{ $priorityConfig['label'] }}
        </span>
    </div>

    {{-- Status Pill --}}
    <div class="flex items-center" @click.stop>
        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold border"
              style="background-color: {{ $stageColor }}15; border-color: {{ $stageColor }}40; color: {{ $stageColor }}">
            <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background-color: {{ $stageColor }}"></span>
            {{ $stageForTask?->name ?? ucfirst(str_replace('_',' ',$task->status)) }}
        </span>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-0.5" @click.stop>
        <button @click="selectedTaskId = {{ $task->id }}" title="Edit Task"
                class="p-1 text-ink-subtle hover:text-ink hover:bg-surface-2 rounded transition-colors opacity-0 group-hover/row:opacity-100">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </button>
        @can('delete', $task)
        <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" onsubmit="return confirm('Delete this task?')">
            @csrf
            @method('DELETE')
            <button type="submit" title="Delete" class="p-1 text-ink-subtle hover:text-red-400 rounded transition-colors opacity-0 group-hover/row:opacity-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </form>
        @endcan
    </div>
</div>
