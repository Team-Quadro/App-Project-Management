@section('title', 'Projects')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h1 class="text-sm font-semibold text-ink">Projects</h1>
        </div>
    </x-slot>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <form method="GET" action="{{ route('projects.index') }}" class="flex flex-1 items-center gap-2 w-full sm:w-auto">
            <div class="relative w-full max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-ink-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..."
                    class="v-input pl-9 bg-canvas" />
            </div>
            <select name="status" onchange="this.form.submit()" class="v-select w-36 bg-canvas">
                <option value="">All Statuses</option>
                @foreach (\App\Models\Project::STATUSES as $status)
                <option value="{{ $status }}" {{ request('status')===$status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            @if (request('search') || request('status'))
            <a href="{{ route('projects.index') }}" class="text-[13px] text-ink-subtle hover:text-ink transition-colors px-2">Clear</a>
            @endif
        </form>

        @can('create', App\Models\Project::class)
        <a href="{{ route('projects.create') }}" class="v-btn-primary text-[13px] gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Project
        </a>
        @endcan
    </div>

    {{-- Project List --}}
    <div class="v-card overflow-hidden">
        @if ($projects->count())
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-hairline text-ink-subtle text-[12px] uppercase tracking-wider">
                        <th class="py-3 px-4 font-medium w-1/3">Project</th>
                        <th class="py-3 px-4 font-medium">Status</th>
                        <th class="py-3 px-4 font-medium">Owner</th>
                        <th class="py-3 px-4 font-medium">Tasks</th>
                        <th class="py-3 px-4 font-medium">Due Date</th>
                        <th class="py-3 px-4 font-medium text-right">Progress</th>
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
                            <x-badge :variant="$project->status" size="xs">{{ ucfirst($project->status) }}</x-badge>
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
                            {{ $project->tasks_count ?? $project->tasks->count() }}
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
                                $totalTasks = $project->tasks_count ?? $project->tasks->count();
                                $doneTasks = $project->tasks->where('status', 'done')->count();
                                $progress = $totalTasks > 0 ? ($doneTasks / $totalTasks) * 100 : 0;
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
            {{ $projects->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <p class="text-[14px] font-medium text-ink mb-1">No projects found</p>
            <p class="text-[13px] text-ink-subtle">Get started by creating your first project.</p>
            @can('create', App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="v-btn-primary text-[13px] mt-4 gap-1.5 inline-flex">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Project
            </a>
            @endcan
        </div>
        @endif
    </div>
</x-app-layout>