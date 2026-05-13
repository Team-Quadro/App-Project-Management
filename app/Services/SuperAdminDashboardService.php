<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;
use Carbon\Carbon;

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
        $activeProjects = Project::where('status', Project::STATUS_ACTIVE)->count();
        $inactiveProjects = Project::whereIn('status', [Project::STATUS_COMPLETED, Project::STATUS_ARCHIVED])->count();

        $totalTasks = Task::count();
        $completedTasks = Task::where('status', Task::STATUS_DONE)->count();
        $incompleteTasks = Task::where('status', '!=', Task::STATUS_DONE)->count();
        $overdueTasks = Task::where('status', '!=', Task::STATUS_DONE)
                            ->whereNotNull('deadline')
                            ->where('deadline', '<', Carbon::now())
                            ->count();

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
        
        $teamWorkload = User::whereNotNull('tenant_id')
            ->withCount([
                'assignedTasks as active_tasks' => fn ($q) => $q->where('status', '!=', Task::STATUS_DONE),
                'assignedTasks as overdue_tasks' => fn ($q) => $q->where('status', '!=', Task::STATUS_DONE)->whereNotNull('deadline')->where('deadline', '<', Carbon::now()),
                'assignedTasks as completed_this_week' => fn ($q) => $q->where('status', Task::STATUS_DONE)->where('updated_at', '>=', Carbon::now()->startOfWeek())
            ])
            ->get()
            ->map(function ($user) {
                $user->workload_score = ($user->active_tasks * 1) + ($user->overdue_tasks * 2);
                return $user;
            })
            ->sortByDesc('workload_score')
            ->take(10);

        return compact(
            'totalTenants',
            'pendingTenants',
            'activeTenants',
            'totalUsers',
            'totalProjects',
            'activeProjects',
            'inactiveProjects',
            'totalTasks',
            'completedTasks',
            'incompleteTasks',
            'overdueTasks',
            'averageCompletionRate',
            'tenantStats',
            'teamWorkload'
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
