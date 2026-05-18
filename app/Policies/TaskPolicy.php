<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Can user create a task in this project?
     * Any project participant (owner or member) can create tasks.
     */
    public function create(User $user, Project $project): bool
    {
        return $user->isSuperAdmin() || $this->isProjectParticipant($user, $project);
    }

    /**
     * Can user update / move the task?
     * Any project participant can update tasks — including regular members (karyawan).
     * Assignees can always update their own tasks.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->isSuperAdmin()
            || $task->assigned_to === $user->id
            || $this->isProjectParticipant($user, $task->project);
    }

    /**
     * Can user delete the task?
     * Only SuperAdmin, the project owner, or the assignee can delete a task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->isSuperAdmin()
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