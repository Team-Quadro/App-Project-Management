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

    /**
     * Update the workflow stage of a task via AJAX (Kanban Drag and Drop).
     */
    public function updateStage(Request $request, Project $project, Task $task)
    {
        // Pastikan user punya akses mengupdate task ini
        $this->authorize('update', $task);

        // Validasi bahwa stage_id yang dikirim benar-benar milik project ini
        $request->validate([
            'stage_id' => [
                'required',
                'exists:workflow_stages,id',
                // Pastikan stage yang dipilih benar-benar milik project tempat task ini berada
                function ($attribute, $value, $fail) use ($project) {
                    $stageBelongsToProject = \App\Models\WorkflowStage::where('id', $value)
                        ->where('project_id', $project->id)
                        ->exists();
                        
                    if (!$stageBelongsToProject) {
                        $fail('The selected workflow stage is invalid for this project.');
                    }
                },
            ],
        ]);

        $task->update([
            'stage_id' => $request->stage_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task stage updated successfully.',
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'new_stage_id' => $task->stage_id
            ]
        ]);
    }
}