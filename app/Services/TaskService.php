<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    /**
     * List tasks for a project with optional filters.
     */
    public function list(Project $project, array $filters = []): LengthAwarePaginator
    {
        return $project->tasks()
            ->search($filters['search'] ?? null)
            ->filterStatus($filters['status'] ?? null)
            ->filterPriority($filters['priority'] ?? null)
            ->filterAssignee($filters['assignee'] ?? null)
            ->with('assignee')
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Create a new task within a project.
     */
    public function create(Project $project, array $data): Task
    {
        return $project->tasks()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? Task::STATUS_TODO,
            'priority' => $data['priority'] ?? Task::PRIORITY_MEDIUM,
            'assigned_to' => $data['assigned_to'] ?? null,
            'deadline' => $data['deadline'] ?? null,
        ]);
    }

    /**
     * Update a task.
     */
    public function update(Task $task, array $data): Task
    {
        $task->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? $task->status,
            'priority' => $data['priority'] ?? $task->priority,
            'assigned_to' => $data['assigned_to'] ?? null,
            'deadline' => $data['deadline'] ?? null,
        ]);

        return $task->fresh();
    }

    /**
     * Update only the status of a task.
     */
    public function updateStatus(Task $task, string $status): Task
    {
        $task->update(['status' => $status]);

        return $task;
    }

    /**
     * Delete a task.
     */
    public function delete(Task $task): void
    {
        $task->delete();
    }
}