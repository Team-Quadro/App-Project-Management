@section('title', 'Holding Dashboard')

<x-app-layout>
    <x-slot name="header">
        <h1 class="text-sm font-semibold text-gray-900">Holding Dashboard</h1>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-stat-card label="Total Tenants" :value="$totalTenants" />
        <x-stat-card label="Pending Approvals" :value="$pendingTenants" />
        <x-stat-card label="Active Tenants" :value="$activeTenants" />
        <x-stat-card label="Total Users" :value="$totalUsers" />
        <x-stat-card label="Total Projects" :value="$totalProjects" />
        <x-stat-card label="Avg. Completion Rate" :value="number_format($averageCompletionRate, 1) . '%'" />
    </div>

    <div class="v-card p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-900">Subsidiary Performance</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-400">
                        <th class="py-2 pr-4">Company</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 pr-4">Projects</th>
                        <th class="py-2 pr-4">Users</th>
                        <th class="py-2">Completion Rate</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse ($tenantStats as $stat)
                        <tr class="border-t border-border">
                            <td class="py-2 pr-4">
                                <div class="font-medium text-gray-900">{{ $stat['tenant']->company_name }}</div>
                            </td>
                            <td class="py-2 pr-4 capitalize">{{ $stat['tenant']->status }}</td>
                            <td class="py-2 pr-4 tabular-nums">{{ $stat['project_count'] }}</td>
                            <td class="py-2 pr-4 tabular-nums">{{ $stat['user_count'] }}</td>
                            <td class="py-2 tabular-nums">{{ number_format($stat['completion_rate'], 1) }}%</td>
                        </tr>
                    @empty
                        <tr class="border-t border-border">
                            <td class="py-4 text-gray-400" colspan="5">No tenants yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
