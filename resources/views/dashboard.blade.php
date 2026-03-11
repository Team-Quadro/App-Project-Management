@section('title', 'Dashboard')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-gray-900">Dashboard</h1>
    </x-slot>

    {{-- Welcome --}}
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Welcome back, {{ Auth::user()->name }}</h2>
        <p class="text-sm text-gray-500 mt-0.5">Here's what's happening with your projects.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="v-card p-4">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Projects</p>
                <div class="w-8 h-8 rounded-[4px] bg-brand-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-2 tabular-nums">{{ $stats['total_projects'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Total projects</p>
        </div>

        <div class="v-card p-4">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Active</p>
                <div class="w-8 h-8 rounded-[4px] bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-2 tabular-nums">{{ $stats['active_projects'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Currently active</p>
        </div>

        <div class="v-card p-4">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tasks</p>
                <div class="w-8 h-8 rounded-[4px] bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-2 tabular-nums">{{ $stats['total_tasks'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Total tasks</p>
        </div>

        <div class="v-card p-4">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">My Open</p>
                <div class="w-8 h-8 rounded-[4px] bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-2 tabular-nums">{{ $stats['my_open_tasks'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Assigned to you</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        {{-- Task breakdown --}}
        <div class="lg:col-span-2 v-card">
            <div class="px-4 py-3 border-b border-border">
                <h3 class="text-sm font-semibold text-gray-900">Task Breakdown</h3>
            </div>
            <div class="p-4 space-y-4">
                @php
                $statuses = [
                    ['label' => 'To Do', 'count' => $stats['tasks_todo'], 'color' => 'bg-gray-400', 'bg' => 'bg-gray-100'],
                    ['label' => 'In Progress', 'count' => $stats['tasks_in_progress'], 'color' => 'bg-brand-500', 'bg' => 'bg-brand-50'],
                    ['label' => 'Done', 'count' => $stats['tasks_done'], 'color' => 'bg-emerald-500', 'bg' => 'bg-emerald-50'],
                ];
                $total = max($stats['total_tasks'], 1);
                @endphp
                @foreach ($statuses as $s)
                <div>
                    <div class="flex items-center justify-between text-sm mb-1.5">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full {{ $s['color'] }}"></div>
                            <span class="text-gray-600">{{ $s['label'] }}</span>
                        </div>
                        <span class="text-gray-900 font-semibold tabular-nums">{{ $s['count'] }}</span>
                    </div>
                    <div class="h-1.5 rounded-full {{ $s['bg'] }} overflow-hidden">
                        <div class="h-full rounded-full {{ $s['color'] }} transition-all duration-500" style="width: {{ ($s['count'] / $total) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Projects --}}
        <div class="lg:col-span-3 v-card">
            <div class="px-4 py-3 border-b border-border">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">Recent Projects</h3>
                    <a href="{{ route('projects.index') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700 transition-colors">View all</a>
                </div>
            </div>
            @if ($recentProjects->count())
            <div class="divide-y divide-border">
                @foreach ($recentProjects as $project)
                <a href="{{ route('projects.show', $project) }}" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition-colors duration-100 group">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 group-hover:text-brand-700 truncate">{{ $project->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $project->owner->name }} · {{ $project->tasks_count ?? 0 }} tasks</p>
                    </div>
                    <x-badge :variant="$project->status">{{ ucfirst($project->status) }}</x-badge>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center py-10 text-sm text-gray-400">No projects yet.</div>
            @endif
        </div>
    </div>

    {{-- Upcoming Deadlines --}}
    @if ($upcomingDeadlines->count())
    <div class="mt-4 v-card">
        <div class="px-4 py-3 border-b border-border">
            <h3 class="text-sm font-semibold text-gray-900">Upcoming Deadlines</h3>
        </div>
        <div class="divide-y divide-border">
            @foreach ($upcomingDeadlines as $task)
            <div class="flex items-center justify-between px-4 py-3">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $task->title }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $task->project->title }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <x-badge :variant="$task->priority">{{ ucfirst($task->priority) }}</x-badge>
                    <span class="text-xs font-medium tabular-nums {{ $task->deadline->isPast() ? 'text-red-500' : 'text-gray-500' }}">
                        {{ $task->deadline->format('M d') }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</x-app-layout>