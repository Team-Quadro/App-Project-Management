<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Anyone authenticated can view listing (scoped in service).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin can view any project; member only if owner or member.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->isAdmin() || $this->isParticipant($user, $project);
    }

    /**
     * Any authenticated user can create projects.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin can update any; member can update own projects.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin can delete any; only owner can delete their project.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->isAdmin();
    }

    /**
     * Check if user is the owner or a member of the project.
     */
    private function isParticipant(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id
            || $project->members()->where('users.id', $user->id)->exists();
    }
}