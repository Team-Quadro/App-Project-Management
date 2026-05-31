<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectService
{
    /**
     * List projects accessible by the user, with optional filters.
     */
    public function list(User $user, array $filters = []): LengthAwarePaginator
    {
        return Project::accessibleBy($user)
            ->search($filters['search'] ?? null)
            ->filterStatus($filters['status'] ?? null)
            ->with('owner')
            ->withCount('tasks')
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Create a new project.
     */
    public function create(array $data, User $owner): Project
    {
        \App\Models\ProjectStage::ensureDefaultsForTenant($owner->tenant_id);

        $defaultStage = \App\Models\ProjectStage::withoutGlobalScopes()
            ->where('tenant_id', $owner->tenant_id)
            ->orderBy('sort_order')
            ->value('key');

        $project = Project::create([
            'tenant_id' => $owner->tenant_id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? Project::STATUS_ACTIVE,
            'stage' => $data['stage'] ?? $defaultStage ?? Project::STAGE_APPROACH,
            'owner_id' => $owner->id,
            'deadline' => $data['deadline'] ?? null,
        ]);

        // Ensure default workflow stages exist for this tenant
        $exists = \App\Models\WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $project->tenant_id)
            ->whereNull('project_id')
            ->exists();

        if (! $exists && $project->tenant_id) {
            \App\Models\WorkflowStage::create([
                'tenant_id'  => $project->tenant_id,
                'project_id' => null,
                'name'       => 'Todo',
                'key'        => 'todo',
                'color'      => '#6b7280',
                'sort_order' => 1,
            ]);
            \App\Models\WorkflowStage::create([
                'tenant_id'  => $project->tenant_id,
                'project_id' => null,
                'name'       => 'Doing',
                'key'        => 'doing',
                'color'      => '#3b82f6',
                'sort_order' => 2,
            ]);
            \App\Models\WorkflowStage::create([
                'tenant_id'  => $project->tenant_id,
                'project_id' => null,
                'name'       => 'Done',
                'key'        => 'done',
                'color'      => '#22c55e',
                'sort_order' => 3,
            ]);
        }

        $this->syncMembers($project, $data['member_ids'] ?? []);

        return $project;
    }

    /**
     * Update an existing project.
     */
    public function update(Project $project, array $data): Project
    {
        $project->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? $project->status,
            'stage' => $data['stage'] ?? $project->stage,
            'deadline' => $data['deadline'] ?? null,
        ]);

        $this->syncMembers($project, $data['member_ids'] ?? []);

        return $project->fresh();
    }

    /**
     * Sync project members by user IDs.
     * Directly attaches selected users — no invitation flow needed.
     */
    private function syncMembers(Project $project, array $memberIds): void
    {
        $memberIds = collect($memberIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id !== $project->owner_id)
            ->unique()
            ->values()
            ->toArray();

        // Sync with pivot data (tenant_id)
        $syncData = [];
        foreach ($memberIds as $id) {
            $syncData[$id] = ['tenant_id' => $project->tenant_id];
        }

        $project->members()->sync($syncData);
    }

    /**
     * Delete a project.
     */
    public function delete(Project $project): void
    {
        $project->delete();
    }
}