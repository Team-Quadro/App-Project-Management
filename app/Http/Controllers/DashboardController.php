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
        $data = $this->dashboardService->getSummary($request->user());

        return view('dashboard', [
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