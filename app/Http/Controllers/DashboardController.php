<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $data = $this->dashboardService->getSummary($request->user());

        $pendingInvitations = \App\Models\ProjectInvitation::where('email', $user->email)
            ->where('status', \App\Models\ProjectInvitation::STATUS_PENDING)
            ->count();

        return view('dashboard', [
            'tenant' => $user->tenant,
            'pendingInvitations' => $pendingInvitations,
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
        ]);
    }
}