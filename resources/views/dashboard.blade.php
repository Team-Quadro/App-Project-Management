@section('title', 'Dashboard')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-ink">Dashboard</h1>
    </x-slot>

    @php
        $hour = now()->format('H');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $user = Auth::user();
    @endphp

    {{-- Greeting --}}
    <div class="mb-7">
        <h2 class="text-2xl font-semibold text-ink tracking-tight">{{ $greeting }}, {{ $user->name }} 👋</h2>
        <p class="text-[13px] text-ink-subtle mt-1">{{ now()->format('l, F j, Y') }} · {{ $tenant?->company_name ?? 'No company' }}</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @php
            $statCards = [
                ['label' => 'My Open Tasks',  'value' => $stats['my_open_tasks'],     'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => '#6366f1'],
                ['label' => 'Total Projects', 'value' => $stats['total_projects'],     'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z', 'color' => '#3b82f6'],
                ['label' => 'Active Projects','value' => $stats['active_projects'],    'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => '#10b981'],
                ['label' => 'Total Tasks',    'value' => $stats['total_tasks'],        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => '#f59e0b'],
            ];
        @endphp
        @foreach ($statCards as $card)
        <div class="v-card p-5 flex flex-col gap-3 group hover:bg-surface-2/60 transition-colors">
            <div class="flex items-center justify-between">
                <span class="text-[12px] font-medium text-ink-subtle uppercase tracking-wider">{{ $card['label'] }}</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: {{ $card['color'] }}20">
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-bold text-ink tabular-nums">{{ $card['value'] }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Projects --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="v-card overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-hairline">
                    <h3 class="text-[13px] font-semibold text-ink">Recent Projects</h3>
                    <a href="{{ route('projects.index') }}" class="text-[12px] text-ink-subtle hover:text-ink transition-colors">View all →</a>
                </div>
                <div class="divide-y divide-hairline">
                    @forelse ($recentProjects as $project)
                    <a href="{{ route('projects.show', $project) }}" class="flex items-center gap-4 px-5 py-3.5 hover:bg-surface-2/50 transition-colors group">
                        <div class="w-8 h-8 rounded-md flex items-center justify-center text-[13px] font-bold text-white shrink-0" style="background: linear-gradient(135deg,#6366f1,#8b5cf6)">
                            {{ strtoupper(substr($project->title, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-medium text-ink group-hover:text-primary transition-colors truncate">{{ $project->title }}</p>
                            <p class="text-[11px] text-ink-subtle">{{ $project->tasks_count }} tasks</p>
                        </div>
                        <x-badge :variant="$project->status" size="xs">{{ ucfirst($project->status) }}</x-badge>
                    </a>
                    @empty
                    <div class="px-5 py-8 text-center text-[13px] text-ink-subtle">No projects yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Task Progress Bar --}}
            <div class="v-card p-5">
                <h3 class="text-[13px] font-semibold text-ink mb-4">Task Breakdown</h3>
                @php
                    $total = $stats['total_tasks'] ?: 1;
                    $bars = [
                        ['label' => 'Todo',        'count' => $stats['tasks_todo'],        'color' => '#6b7280'],
                        ['label' => 'In Progress',  'count' => $stats['tasks_in_progress'], 'color' => '#6366f1'],
                        ['label' => 'Done',         'count' => $stats['tasks_done'],        'color' => '#10b981'],
                    ];
                @endphp
                <div class="flex h-2.5 rounded-full overflow-hidden mb-4 gap-0.5 bg-surface-2">
                    @foreach($bars as $bar)
                    @if($bar['count'] > 0)
                    <div class="h-full rounded-full transition-all" style="width:{{ ($bar['count']/$total)*100 }}%; background-color: {{ $bar['color'] }};"></div>
                    @endif
                    @endforeach
                </div>
                <div class="flex gap-5">
                    @foreach($bars as $bar)
                    <div class="flex items-center gap-1.5 text-[12px] text-ink-subtle">
                        <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $bar['color'] }}"></span>
                        {{ $bar['label'] }} ({{ $bar['count'] }})
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">
            {{-- Company Card --}}
            <div class="v-card p-5">
                <h3 class="text-[13px] font-semibold text-ink mb-4">Company</h3>
                @if($tenant)
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center text-primary font-bold text-lg">
                        {{ strtoupper(substr($tenant->company_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-medium text-ink text-[14px]">{{ $tenant->company_name }}</p>
                        <p class="text-[12px] text-ink-subtle">{{ $tenant->industry ?? 'N/A' }}</p>
                    </div>
                </div>
                <dl class="space-y-2.5 text-[13px]">
                    <div class="flex justify-between">
                        <dt class="text-ink-subtle">PIC</dt>
                        <dd class="text-ink font-medium truncate ml-2">{{ $tenant->pic_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-subtle">Status</dt>
                        <dd><x-badge :variant="$tenant->status === 'approved' ? 'success' : 'in_progress'" size="xs">{{ ucfirst($tenant->status) }}</x-badge></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-ink-subtle">Users</dt>
                        <dd class="text-ink font-medium tabular-nums">{{ $tenant->estimated_users ?? '—' }}</dd>
                    </div>
                </dl>
                @else
                <p class="text-[13px] text-ink-subtle">No company assigned.</p>
                @endif
            </div>

            {{-- Upcoming Deadlines --}}
            <div class="v-card overflow-hidden">
                <div class="px-5 py-4 border-b border-hairline">
                    <h3 class="text-[13px] font-semibold text-ink">Upcoming Deadlines</h3>
                </div>
                <div class="divide-y divide-hairline">
                    @forelse ($upcomingDeadlines as $task)
                    <div class="px-5 py-3 flex items-start gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] text-ink font-medium truncate">{{ $task->title }}</p>
                            <p class="text-[11px] text-ink-subtle mt-0.5">{{ $task->project->title }}</p>
                        </div>
                        <span class="text-[11px] tabular-nums {{ $task->deadline->diffInDays(now()) <= 2 ? 'text-red-400' : 'text-ink-subtle' }} shrink-0">
                            {{ $task->deadline->format('M j') }}
                        </span>
                    </div>
                    @empty
                    <div class="px-5 py-6 text-center text-[13px] text-ink-subtle">All clear! 🎉</div>
                    @endforelse
                </div>
            </div>

            {{-- Team Workload for PIC --}}
            @if(Auth::user()->isCompanyAdmin() && $teamWorkload->isNotEmpty())
            <div class="v-card p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[13px] font-semibold text-ink">Team Workload</h3>
                    <span class="text-[11px] text-ink-subtle uppercase tracking-wider font-medium">Active Tasks</span>
                </div>
                <div class="space-y-4">
                    @foreach ($teamWorkload as $member)
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-[13px]">
                            <div>
                                <span class="font-medium text-ink block leading-tight">{{ $member['name'] }}</span>
                                <span class="text-[11px] text-ink-subtle leading-tight">{{ $member['job_title'] }}</span>
                            </div>
                            <div class="text-right shrink-0 ml-2">
                                <span class="text-ink font-semibold tabular-nums">{{ $member['active_tasks'] }}</span>
                                <span class="text-ink-muted text-[11px]">/{{ $member['total_tasks'] }} active</span>
                            </div>
                        </div>
                        <div class="h-2 rounded-full bg-surface-2 overflow-hidden flex">
                            @php
                                $pct = $member['workload_percentage'];
                                // Custom workload indicator colors: Red for overloaded, Yellow for moderate, Indigo for light
                                $color = $pct > 75 ? '#f87171' : ($pct > 40 ? '#f59e0b' : '#6366f1');
                            @endphp
                            <div class="h-full rounded-full transition-all duration-300" style="width: {{ $pct }}%; background-color: {{ $color }};"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>