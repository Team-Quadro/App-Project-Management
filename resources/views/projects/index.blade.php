@section('title', 'Projects')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h1 class="text-sm font-semibold text-gray-900">Projects</h1>
        </div>
    </x-slot>

    {{-- Filters --}}
    <form method="GET" action="{{ route('projects.index') }}" class="mb-6">
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1 min-w-0">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..."
                    class="v-input pl-9" />
            </div>
            <select name="status" onchange="this.form.submit()" class="v-select !w-auto sm:w-40 shrink-0">
                <option value="">All Statuses</option>
                @foreach (\App\Models\Project::STATUSES as $status)
                <option value="{{ $status }}" {{ request('status')===$status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="v-btn-primary text-[13px] shrink-0">Search</button>
            @if (request('search') || request('status'))
            <a href="{{ route('projects.index') }}" class="v-btn-secondary text-[13px] shrink-0">Clear</a>
            @endif
            @can('create', App\Models\Project::class)
            <a href="{{ route('projects.create') }}" class="v-btn-primary text-[13px] gap-1.5 shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Project
            </a>
            @endcan
        </div>
    </form>

    {{-- Project Cards --}}
    @if ($projects->count())
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($projects as $project)
        <a href="{{ route('projects.show', $project) }}"
            class="group v-card p-4 flex flex-col hover:border-brand-200 hover:shadow-sm transition-all duration-100">
            <div class="flex items-start justify-between gap-3 mb-2">
                <h3 class="text-[13px] font-semibold text-gray-900 group-hover:text-brand-700 line-clamp-2">{{ $project->title }}</h3>
                <x-badge :variant="$project->status" size="xs">{{ ucfirst($project->status) }}</x-badge>
            </div>

            @if ($project->description)
            <p class="text-[12px] text-gray-500 line-clamp-2 mb-4 flex-1">{{ $project->description }}</p>
            @else
            <div class="flex-1"></div>
            @endif

            <div class="flex items-center justify-between pt-3 border-t border-border">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 rounded-full bg-brand-600 text-white text-[9px] font-medium flex items-center justify-center">
                        {{ strtoupper(substr($project->owner->name, 0, 1)) }}
                    </div>
                    <span class="text-[12px] text-gray-500">{{ $project->owner->name }}</span>
                </div>
                <div class="flex items-center gap-3 text-[12px] text-gray-400">
                    <span class="tabular-nums">{{ $project->tasks_count ?? $project->tasks->count() }} tasks</span>
                    @if ($project->deadline)
                    <span class="{{ $project->deadline->isPast() ? 'text-red-500' : '' }} tabular-nums">
                        {{ $project->deadline->format('M d') }}
                    </span>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $projects->withQueryString()->links() }}
    </div>
    @else
    <div class="text-center py-16">
        <p class="text-[13px] text-gray-500 mb-1">No projects found.</p>
        <p class="text-[12px] text-gray-400">Get started by creating your first project.</p>
        @can('create', App\Models\Project::class)
        <a href="{{ route('projects.create') }}" class="v-btn-primary text-[13px] mt-4 gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Project
        </a>
        @endcan
    </div>
    @endif
</x-app-layout>