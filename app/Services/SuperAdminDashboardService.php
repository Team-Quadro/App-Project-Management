<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;

class SuperAdminDashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        $totalTenants = Tenant::count();
        $pendingTenants = Tenant::where('status', Tenant::STATUS_PENDING)->count();
        $activeTenants = Tenant::where('status', Tenant::STATUS_APPROVED)->count();

        $totalUsers = User::whereNotNull('tenant_id')->count();
        $totalProjects = Project::count();

        $projects = Project::withCount([
            'tasks as total_tasks',
            'tasks as done_tasks' => fn ($query) => $query->where('status', Task::STATUS_DONE),
        ])->get();

        $averageCompletionRate = $projects->count() === 0
            ? 0
            : round($projects->avg(function (Project $project) {
                return $project->total_tasks > 0
                    ? ($project->done_tasks / $project->total_tasks) * 100
                    : 0;
            }), 1);

        $tenantStats = $this->buildTenantStats();

        return compact(
            'totalTenants',
            'pendingTenants',
            'activeTenants',
            'totalUsers',
            'totalProjects',
            'averageCompletionRate',
            'tenantStats',
        );
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function buildTenantStats(): Collection
    {
        return Tenant::with(['projects.tasks', 'users'])
            ->get()
            ->map(function (Tenant $tenant) {
                $projects = $tenant->projects;
                $totalTasks = $projects->flatMap->tasks->count();
                $doneTasks = $projects->flatMap->tasks->where('status', Task::STATUS_DONE)->count();

                $completionRate = $totalTasks > 0
                    ? round(($doneTasks / $totalTasks) * 100, 1)
                    : 0;

                return [
                    'tenant' => $tenant,
                    'project_count' => $projects->count(),
                    'user_count' => $tenant->users->count(),
                    'completion_rate' => $completionRate,
                ];
            });
    }
}
