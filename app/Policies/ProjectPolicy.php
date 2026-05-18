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
     * SuperAdmin can view any project; PIC and Member only if owner or member.
     */
    public function view(User $user, Project $project): bool
    {
        return $user->isSuperAdmin() || $this->isParticipant($user, $project);
    }

    /**
     * Only SuperAdmin and PIC can create projects.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isCompanyAdmin();
    }

    /**
     * Only SuperAdmin or the project's owner (PIC) can update project details.
     * Regular members (karyawan) cannot edit project metadata.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->isSuperAdmin()
            || ($user->isCompanyAdmin() && $project->owner_id === $user->id);
    }

    /**
     * Only SuperAdmin or the project owner can delete a project.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->isSuperAdmin()
            || ($user->isCompanyAdmin() && $project->owner_id === $user->id);
    }

    /**
     * Can the user manage tasks within this project?
     * Any project participant (owner OR member, regardless of role) can
     * create / update / move tasks. This is separate from editing project metadata.
     */
    public function updateTasks(User $user, Project $project): bool
    {
        return $user->isSuperAdmin() || $this->isParticipant($user, $project);
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