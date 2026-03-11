@section('title', $project->title)

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1.5 text-[13px]">
            <a href="{{ route('projects.index') }}" class="text-gray-500 hover:text-brand-600 transition-colors duration-100">Projects</a>
            <span class="text-gray-300">/</span>
            <span class="font-medium text-gray-900">{{ $project->title }}</span>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Project Header --}}
            <div class="v-card p-5">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h1 class="text-base font-semibold text-gray-900">{{ $project->title }}</h1>
                            <x-badge :variant="$project->status">{{ ucfirst($project->status) }}</x-badge>
                        </div>
                        <p class="text-[12px] text-gray-400">Created {{ $project->created_at->diffForHumans() }}</p>
                    </div>
                    @can('update', $project)
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('projects.edit', $project) }}" class="v-btn-secondary text-[13px] py-1.5 px-3">Edit</a>
                        @can('delete', $project)
                        <x-confirm-delete :action="route('projects.destroy', $project)" title="Delete Project" message="This will permanently delete this project and all its tasks." />
                        @endcan
                    </div>
                    @endcan
                </div>

                @if ($project->description)
                <p class="text-[13px] text-gray-600 leading-relaxed">{{ $project->description }}</p>
                @endif
            </div>

            {{-- Tasks --}}
            <div class="v-card">
                <div class="flex items-center justify-between px-4 py-3 border-b border-border">
                    <h3 class="text-[13px] font-semibold text-gray-900">Tasks</h3>
                    @can('update', $project)
                    <a href="{{ route('projects.tasks.create', $project) }}" class="v-btn-primary text-[12px] py-1 px-2.5 gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Task
                    </a>
                    @endcan
                </div>

                {{-- Task Filters --}}
                <div class="px-4 py-2.5 border-b border-border bg-surface">
                    <form method="GET" action="{{ route('projects.show', $project) }}" class="flex flex-wrap gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tasks..."
                            class="v-input flex-1 min-w-[140px] text-[12px] py-1.5 px-2.5" />
                        <select name="status" onchange="this.form.submit()" class="v-select text-[12px] py-1.5 px-2.5 w-auto">
                            <option value="">All Status</option>
                            @foreach (\App\Models\Task::STATUSES as $s)
                            <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                        <select name="priority" onchange="this.form.submit()" class="v-select text-[12px] py-1.5 px-2.5 w-auto">
                            <option value="">All Priority</option>
                            @foreach (\App\Models\Task::PRIORITIES as $p)
                            <option value="{{ $p }}" {{ request('priority')===$p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="v-btn-primary text-[12px] py-1.5 px-2.5">Filter</button>
                        @if (request()->hasAny(['search', 'status', 'priority']))
                        <a href="{{ route('projects.show', $project) }}" class="v-btn-secondary text-[12px] py-1.5 px-2.5">Clear</a>
                        @endif
                    </form>
                </div>

                {{-- Task List --}}
                @if ($tasks->count())
                <div class="divide-y divide-border">
                    @foreach ($tasks as $task)
                    <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors duration-100 group">
                        <form method="POST" action="{{ route('projects.tasks.status', [$project, $task]) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $task->status === 'done' ? 'todo' : ($task->status === 'todo' ? 'in_progress' : 'done') }}">
                            <button type="submit" title="Toggle status"
                                class="w-4 h-4 rounded-[3px] border flex items-center justify-center transition-colors duration-100
                                    {{ $task->status === 'done' ? 'bg-brand-600 border-brand-600 text-white' : ($task->status === 'in_progress' ? 'border-brand-400 bg-brand-50' : 'border-gray-300 hover:border-brand-500') }}">
                                @if ($task->status === 'done')
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                @elseif ($task->status === 'in_progress')
                                <div class="w-1.5 h-1.5 rounded-full bg-brand-400"></div>
                                @endif
                            </button>
                        </form>

                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-medium {{ $task->status === 'done' ? 'text-gray-400 line-through' : 'text-gray-900' }} truncate">{{ $task->title }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                @if ($task->assignee)
                                <span class="text-[11px] text-gray-400">{{ $task->assignee->name }}</span>
                                @endif
                                @if ($task->deadline)
                                <span class="text-[11px] {{ $task->deadline->isPast() && $task->status !== 'done' ? 'text-red-500' : 'text-gray-400' }} tabular-nums">
                                    {{ $task->deadline->format('M d') }}
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="hidden sm:flex items-center gap-1 shrink-0">
                            <x-badge :variant="$task->priority" size="xs">{{ ucfirst($task->priority) }}</x-badge>
                            <x-badge :variant="$task->status" size="xs">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</x-badge>
                        </div>

                        @can('update', $task)
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-100 shrink-0">
                            <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="p-1 text-gray-400 hover:text-brand-600 rounded-[3px] hover:bg-brand-50 transition-colors duration-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                            @can('delete', $task)
                            <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" onsubmit="return confirm('Delete this task?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-gray-400 hover:text-red-600 rounded-[3px] hover:bg-red-50 transition-colors duration-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                            @endcan
                        </div>
                        @endcan
                    </div>
                    @endforeach
                </div>
                @if ($tasks->hasPages())
                <div class="px-4 py-3 border-t border-border">
                    {{ $tasks->withQueryString()->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-10">
                    <p class="text-[13px] text-gray-400">No tasks yet.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Right sidebar --}}
        <div class="space-y-6">
            <x-card title="Details">
                <dl class="space-y-3 text-[13px]">
                    <div>
                        <dt class="text-gray-500">Owner</dt>
                        <dd class="flex items-center gap-2 mt-0.5">
                            <div class="w-5 h-5 rounded-full bg-brand-600 text-white text-[9px] font-medium flex items-center justify-center">
                                {{ strtoupper(substr($project->owner->name, 0, 1)) }}
                            </div>
                            <span class="text-gray-900">{{ $project->owner->name }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd class="mt-0.5"><x-badge :variant="$project->status">{{ ucfirst($project->status) }}</x-badge></dd>
                    </div>
                    @if ($project->deadline)
                    <div>
                        <dt class="text-gray-500">Deadline</dt>
                        <dd class="text-gray-900 mt-0.5 tabular-nums {{ $project->deadline->isPast() ? 'text-red-600' : '' }}">
                            {{ $project->deadline->format('F j, Y') }}
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-gray-500">Tasks</dt>
                        <dd class="text-gray-900 mt-0.5">{{ $project->tasks->count() }} total</dd>
                    </div>
                </dl>
            </x-card>

            <x-card title="Team">
                <div class="space-y-2.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-brand-600 text-white text-[9px] font-medium flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($project->owner->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-medium text-gray-900 truncate">{{ $project->owner->name }}</p>
                            <p class="text-[11px] text-gray-400">Owner</p>
                        </div>
                    </div>
                    @foreach ($project->members as $member)
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-gray-200 text-gray-600 text-[9px] font-medium flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[13px] font-medium text-gray-900 truncate">{{ $member->name }}</p>
                            <p class="text-[11px] text-gray-400">Member</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>