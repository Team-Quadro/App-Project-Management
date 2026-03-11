<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;

class DashboardService
{
    /**
     * Get dashboard summary data for the given user.
     *
     * @return array<string, mixed>
     */
    public function getSummary(User $user): array
    {
        $projects = $user->isAdmin()
            ?\App\Models\Project::query()
            : $user->accessibleProjects();

        $projectCount = (clone $projects)->count();
        $activeProjects = (clone $projects)->where('status', 'active')->count();

        // Task counts
        $taskQuery = $user->isAdmin()
            ?Task::query()
            : Task::whereIn('project_id', (clone $projects)->select('id'));

        $totalTasks = (clone $taskQuery)->count();
        $todoTasks = (clone $taskQuery)->where('status', Task::STATUS_TODO)->count();
        $inProgressTasks = (clone $taskQuery)->where('status', Task::STATUS_IN_PROGRESS)->count();
        $doneTasks = (clone $taskQuery)->where('status', Task::STATUS_DONE)->count();

        // My assigned tasks
        $myTasks = $user->assignedTasks()->where('status', '!=', Task::STATUS_DONE)->count();

        // Recent projects
        $recentProjects = (clone $projects)
            ->with('owner')
            ->withCount('tasks')
            ->latest()
            ->take(5)
            ->get();

        // Upcoming deadlines
        $upcomingTasks = Task::whereIn('project_id', (clone $projects)->select('id'))
            ->whereNotNull('deadline')
            ->where('status', '!=', Task::STATUS_DONE)
            ->where('deadline', '>=', now())
            ->orderBy('deadline')
            ->with(['project', 'assignee'])
            ->take(5)
            ->get();

        return compact(
            'projectCount',
            'activeProjects',
            'totalTasks',
            'todoTasks',
            'inProgressTasks',
            'doneTasks',
            'myTasks',
            'recentProjects',
            'upcomingTasks',
        );
    }
}