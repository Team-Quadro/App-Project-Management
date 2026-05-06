<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\WorkflowStage;
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
        $stageId = $data['stage_id'] ?? $this->resolveStageIdFromStatus($project, $data['status'] ?? Task::STATUS_TODO);

        return $project->tasks()->create([
            'tenant_id' => $project->tenant_id,
            'stage_id' => $stageId,
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
        $project = $task->project()->with('stages')->first();
        $stageId = $data['stage_id'] ?? ($project ? $this->resolveStageIdFromStatus($project, $data['status'] ?? $task->status) : $task->stage_id);

        $task->update([
            'stage_id' => $stageId,
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
        $project = $task->project()->with('stages')->first();
        $stageId = $project ? $this->resolveStageIdFromStatus($project, $status) : null;

        $task->update(array_filter([
            'status' => $status,
            'stage_id' => $stageId,
        ], static fn ($value) => ! is_null($value)));

        return $task;
    }

    private function resolveStageIdFromStatus(Project $project, string $status): ?int
    {
        $stage = $project->stages->first(function (WorkflowStage $stage) use ($status) {
            return $stage->key === $status;
        });

        return $stage?->id ?? $project->stages->first()?->id;
    }

    /**
     * Delete a task.
     */
    public function delete(Task $task): void
    {
        $task->delete();
    }
}