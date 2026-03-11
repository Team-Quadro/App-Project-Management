<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {}

    /**
     * Show the form for creating a new task.
     */
    public function create(Project $project)
    {
        $this->authorize('create', [Task::class, $project]);

        $projectUsers = collect([$project->owner])
            ->merge($project->members)
            ->unique('id')
            ->sortBy('name');

        return view('tasks.create', compact('project', 'projectUsers'));
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        $this->authorize('create', [Task::class, $project]);

        $this->taskService->create($project, $request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Task created successfully.');
    }

    /**
     * Show the form for editing an existing task.
     */
    public function edit(Project $project, Task $task)
    {
        $this->authorize('update', $task);

        $projectUsers = collect([$project->owner])
            ->merge($project->members)
            ->unique('id')
            ->sortBy('name');

        return view('tasks.edit', compact('project', 'task', 'projectUsers'));
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        $this->authorize('update', $task);

        $this->taskService->update($task, $request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Project $project, Task $task)
    {
        $this->authorize('delete', $task);

        $this->taskService->delete($task);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Update only the task status (inline from project detail).
     */
    public function updateStatus(Request $request, Project $project, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'status' => ['required', 'in:' . implode(',', Task::STATUSES)],
        ]);

        $this->taskService->updateStatus($task, $request->input('status'));

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Task status updated.');
    }
}