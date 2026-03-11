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
        $project = Project::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? Project::STATUS_ACTIVE,
            'owner_id' => $owner->id,
            'deadline' => $data['deadline'] ?? null,
        ]);

        // Attach members if provided
        if (!empty($data['members'])) {
            $project->members()->sync($data['members']);
        }

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
            'deadline' => $data['deadline'] ?? null,
        ]);

        // Sync members
        if (isset($data['members'])) {
            $project->members()->sync($data['members']);
        }

        return $project->fresh();
    }

    /**
     * Delete a project.
     */
    public function delete(Project $project): void
    {
        $project->delete();
    }
}