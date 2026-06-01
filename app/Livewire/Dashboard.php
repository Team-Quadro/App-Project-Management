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
        
        // Sekarang PIC dan Superadmin sama-sama bisa melihat workload
        if ($user->isCompanyAdmin() || $user->isSuperAdmin()) {
            
            $query = User::with('tenant'); // Tarik relasi tenant untuk UI Superadmin

            if ($user->isCompanyAdmin()) {
                $query->where('tenant_id', $user->tenant_id);
            } else {
                // Jika Superadmin, tarik semua user yang punya perusahaan
                $query->whereNotNull('tenant_id');
            }

            $teamWorkload = $query->withCount([
                    'assignedTasks as total_tasks',
                    'assignedTasks as active_tasks' => function ($q) {
                        $q->where('status', '!=', Task::STATUS_DONE);
                    },
                    'assignedTasks as completed_tasks' => function ($q) {
                        $q->where('status', Task::STATUS_DONE);
                    }
                ])
                ->orderByDesc('active_tasks') // Urutkan dari yang tugasnya paling banyak
                ->take(10) // Batasi 10 orang agar dashboard tetap rapi
                ->get()
                ->map(function ($member) {
                    $total = $member->total_tasks;
                    $active = $member->active_tasks;
                    return [
                        'name' => $member->name,
                        'job_title' => $member->companyRole?->name ?? $member->job_title ?? 'Team Member',
                        'company' => $member->tenant?->company_name, // Disiapkan untuk Superadmin
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
