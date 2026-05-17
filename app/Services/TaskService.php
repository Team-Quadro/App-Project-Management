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
        $stageId = $data['stage_id'] ?? $this->resolveStageId($project->tenant_id, $data['status'] ?? Task::STATUS_TODO);

        return $project->tasks()->create([
            'tenant_id'   => $project->tenant_id,
            'stage_id'    => $stageId,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'status'      => $data['status'] ?? Task::STATUS_TODO,
            'priority'    => $data['priority'] ?? Task::PRIORITY_MEDIUM,
            'assigned_to' => $data['assigned_to'] ?? null,
            'deadline'    => $data['deadline'] ?? null,
        ]);
    }

    /**
     * Update a task.
     */
    public function update(Task $task, array $data): Task
    {
        $tenantId = $task->tenant_id ?? $task->project?->tenant_id;
        $newStatus = $data['status'] ?? $task->status;
        $stageId = $data['stage_id'] ?? $this->resolveStageId($tenantId, $newStatus);

        $task->update([
            'stage_id'    => $stageId,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'status'      => $newStatus,
            'priority'    => $data['priority'] ?? $task->priority,
            'assigned_to' => $data['assigned_to'] ?? null,
            'deadline'    => $data['deadline'] ?? null,
        ]);

        return $task->fresh();
    }

    /**
     * Update only the status of a task.
     */
    public function updateStatus(Task $task, string $status): Task
    {
        $tenantId = $task->tenant_id ?? $task->project?->tenant_id;
        $stageId = $this->resolveStageId($tenantId, $status);

        $task->update([
            'status'   => $status,
            'stage_id' => $stageId,
        ]);

        return $task;
    }

    /**
     * Move a task to a different stage (drag & drop).
     */
    public function moveToStage(Task $task, int $stageId): Task
    {
        $stage = WorkflowStage::withoutGlobalScopes()->find($stageId);
        if (!$stage) {
            return $task;
        }

        $task->update([
            'stage_id' => $stageId,
            'status'   => $stage->key,
        ]);

        return $task;
    }

    /**
     * Resolve stage ID from tenant-wide workflow stages by status key.
     */
    private function resolveStageId(?int $tenantId, string $status): ?int
    {
        if (!$tenantId) {
            return null;
        }

        $stage = WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->whereNull('project_id')
            ->where('key', $status)
            ->first();

        // Fallback to first stage if exact key not found
        if (!$stage) {
            $stage = WorkflowStage::withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->whereNull('project_id')
                ->orderBy('sort_order')
                ->first();
        }

        return $stage?->id;
    }

    /**
     * Delete a task.
     */
    public function delete(Task $task): void
    {
        $task->delete();
    }
}