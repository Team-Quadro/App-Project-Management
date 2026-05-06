<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Tenant;

class SuperAdminTenantService
{
    /**
     * @return array<string, mixed>
     */
    public function overview(Tenant $tenant): array
    {
        $tenant->loadMissing(['projects.tasks', 'users']);

        $projects = $tenant->projects;
        $totalTasks = $projects->flatMap->tasks->count();
        $doneTasks = $projects->flatMap->tasks->where('status', Task::STATUS_DONE)->count();

        $completionRate = $totalTasks > 0
            ? round(($doneTasks / $totalTasks) * 100, 1)
            : 0;

        return [
            'tenant' => $tenant,
            'projectCount' => $projects->count(),
            'userCount' => $tenant->users->count(),
            'totalTasks' => $totalTasks,
            'doneTasks' => $doneTasks,
            'completionRate' => $completionRate,
        ];
    }
}
