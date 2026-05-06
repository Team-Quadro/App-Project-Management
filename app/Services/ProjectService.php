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
            'tenant_id' => $owner->tenant_id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? Project::STATUS_ACTIVE,
            'owner_id' => $owner->id,
            'deadline' => $data['deadline'] ?? null,
        ]);

        $this->inviteMembers($project, $data['member_emails'] ?? [], $owner);

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

        $this->inviteMembers($project, $data['member_emails'] ?? [], $project->owner);

        return $project->fresh();
    }

    /**
     * @param array<int, string> $emails
     */
    private function inviteMembers(Project $project, array $emails, User $inviter): void
    {
        if (empty($emails)) {
            return;
        }

        $emails = collect($emails)
            ->map(fn ($email) => strtolower(trim($email)))
            ->filter()
            ->unique()
            ->values();

        $users = User::whereIn('email', $emails)
            ->where('tenant_id', $project->tenant_id)
            ->get();

        foreach ($users as $user) {
            if ($user->id === $inviter->id) {
                continue;
            }

            if ($project->members()->where('users.id', $user->id)->exists()) {
                continue;
            }

            if ($project->invitations()
                ->where('email', $user->email)
                ->where('status', \App\Models\ProjectInvitation::STATUS_PENDING)
                ->exists()) {
                continue;
            }

            $project->invitations()->create([
                'tenant_id' => $project->tenant_id,
                'email' => $user->email,
                'invited_by' => $inviter->id,
                'status' => \App\Models\ProjectInvitation::STATUS_PENDING,
            ]);
        }
    }

    /**
     * Delete a project.
     */
    public function delete(Project $project): void
    {
        $project->delete();
    }
}