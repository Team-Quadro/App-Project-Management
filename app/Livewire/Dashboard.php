<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\DashboardService;
use App\Models\User;
use App\Models\Task;

#[Layout('layouts.app')]
#[\Livewire\Attributes\Title('Dashboard')]
class Dashboard extends Component
{
    public function render(DashboardService $dashboardService)
    {
        $user = auth()->user();
        $data = $dashboardService->getSummary($user);

        $teamWorkload = collect();
        if ($user->isCompanyAdmin() && $user->tenant_id) {
            $teamWorkload = User::where('tenant_id', $user->tenant_id)
                ->withCount([
                    'assignedTasks as total_tasks',
                    'assignedTasks as active_tasks' => function ($query) {
                        $query->where('status', '!=', Task::STATUS_DONE);
                    },
                    'assignedTasks as completed_tasks' => function ($query) {
                        $query->where('status', Task::STATUS_DONE);
                    }
                ])
                ->get()
                ->map(function ($member) {
                    $total = $member->total_tasks;
                    $active = $member->active_tasks;
                    return [
                        'name' => $member->name,
                        'job_title' => $member->job_title ?? 'Team Member',
                        'active_tasks' => $active,
                        'completed_tasks' => $member->completed_tasks,
                        'total_tasks' => $total,
                        'workload_percentage' => $total > 0 ? min(100, round(($active / $total) * 100)) : 0,
                    ];
                });
        }

        return view('livewire.dashboard', [
            'tenant' => $user->tenant,
            'stats' => [
                'total_projects'    => $data['projectCount'],
                'active_projects'   => $data['activeProjects'],
                'total_tasks'       => $data['totalTasks'],
                'tasks_todo'        => $data['todoTasks'],
                'tasks_in_progress' => $data['inProgressTasks'],
                'tasks_done'        => $data['doneTasks'],
                'my_open_tasks'     => $data['myTasks'],
            ],
            'recentProjects'    => $data['recentProjects'],
            'upcomingDeadlines' => $data['upcomingTasks'],
            'teamWorkload'      => $teamWorkload,
        ]);
    }
}
