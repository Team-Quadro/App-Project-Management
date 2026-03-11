<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Can user create a task in this project?
     */
    public function create(User $user, Project $project): bool
    {
        return $user->isAdmin() || $this->isProjectParticipant($user, $project);
    }

    /**
     * Can user update the task?
     */
    public function update(User $user, Task $task): bool
    {
        return $user->isAdmin()
            || $this->isProjectParticipant($user, $task->project);
    }

    /**
     * Can user delete the task?
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->isAdmin()
            || $task->project->owner_id === $user->id
            || $task->assigned_to === $user->id;
    }

    /**
     * Check if user is the owner or a member of the project.
     */
    private function isProjectParticipant(User $user, Project $project): bool
    {
        return $project->owner_id === $user->id
            || $project->members()->where('users.id', $user->id)->exists();
    }
}