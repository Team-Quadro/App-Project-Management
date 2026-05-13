@section('title', 'Holding Dashboard')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-ink">Holding Dashboard</h1>
    </x-slot>

    @php
        $hour = now()->format('H');
        $greeting = 'Good evening';
        if ($hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour < 17) {
            $greeting = 'Good afternoon';
        }
    @endphp

    <div class="mb-8">
        <h2 class="text-2xl font-semibold text-ink tracking-tight">{{ $greeting }}, {{ Auth::user()->name }}</h2>
        <p class="text-ink-subtle mt-1 text-sm">Here is the latest overview of your holding companies and projects.</p>
    </div>

    <!-- 4x2 Bento Box -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <x-stat-card label="Total Projects" :value="$totalProjects" />
        <x-stat-card label="Avg Progress" :value="number_format($averageCompletionRate, 1) . '%'" />
        <x-stat-card label="Active Projects" :value="$activeProjects" />
        <x-stat-card label="Inactive Projects" :value="$inactiveProjects" />
        <x-stat-card label="Total Tasks" :value="$totalTasks" />
        <x-stat-card label="Completed Tasks" :value="$completedTasks" />
        <x-stat-card label="Incomplete Tasks" :value="$incompleteTasks" />
        <x-stat-card label="Overdue Tasks" :value="$overdueTasks" class="{{ $overdueTasks > 0 ? 'text-red-400' : '' }}" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Team Workload -->
        <div class="v-card p-5">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-semibold text-ink">Team Workload</h2>
                <a href="#" class="text-xs text-primary hover:text-primary-hover transition-colors">View all</a>
            </div>
            <div class="space-y-4">
                @forelse ($teamWorkload as $user)
                <div class="flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-surface-2 flex items-center justify-center text-xs font-medium text-ink shrink-0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-[13px] font-medium text-ink truncate">{{ $user->name }}</p>
                            <p class="text-[11px] text-ink-subtle truncate">{{ $user->active_tasks }} active • {{ $user->overdue_tasks }} overdue</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-surface-2 {{ $user->workload_score > 10 ? 'text-red-400 border border-red-500/20' : 'text-ink-subtle border border-hairline' }}">
                            Score: {{ $user->workload_score }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-[13px] text-ink-subtle text-center py-8">No team members found.</p>
                @endforelse
            </div>
        </div>

        <!-- Subsidiary Performance -->
        <div class="v-card p-5 flex flex-col">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-semibold text-ink">Subsidiary Performance</h2>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-ink-subtle border-b border-hairline">
                            <th class="py-2 pr-4 font-medium text-xs">Company</th>
                            <th class="py-2 pr-4 font-medium text-xs text-right">Projects</th>
                            <th class="py-2 font-medium text-xs w-32">Completion</th>
                        </tr>
                    </thead>
                    <tbody class="text-ink">
                        @forelse ($tenantStats as $stat)
                            <tr class="border-b border-hairline last:border-0 hover:bg-surface-2/50 transition-colors">
                                <td class="py-3 pr-4">
                                    <div class="font-medium text-[13px] text-ink">{{ $stat['tenant']->company_name }}</div>
                                    <div class="text-[11px] text-ink-subtle capitalize">{{ $stat['tenant']->status }}</div>
                                </td>
                                <td class="py-3 pr-4 tabular-nums text-right text-[13px]">{{ $stat['project_count'] }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 bg-surface-2 rounded-full overflow-hidden">
                                            <div class="h-full bg-primary rounded-full transition-all duration-500" style="width: {{ $stat['completion_rate'] }}%"></div>
                                        </div>
                                        <span class="text-[12px] text-ink-subtle w-9 text-right tabular-nums">{{ number_format($stat['completion_rate'], 0) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-8 text-center text-ink-subtle text-[13px]" colspan="3">No subsidiaries yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
