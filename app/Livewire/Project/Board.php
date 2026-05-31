<?php

namespace App\Livewire\Project;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Project;
use App\Models\Task;
use App\Models\WorkflowStage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('layouts.app')]
class Board extends Component
{
    use AuthorizesRequests;

    public Project $project;

    public $search = '';
    public $task_status = '';

    // Quick Add Task
    public $addingTaskGroup = null;
    public $newTaskTitle = '';
    public $newTaskAssignee = '';
    public $newTaskDeadline = '';
    public $newTaskPriority = 'medium';

    // Quick Add Section
    public $addingSection = false;
    public $newSectionName = '';
    public $newSectionColor = '#6366f1';
    public $newSectionIsActive = false; // <-- TAMBAHAN: Properti baru untuk status aktif

    // Sidebar
    public $selectedTaskId = null;
    public ?Task $editingTask = null;
    public $editingTaskTitle = '';
    public $editingTaskAssignee = '';
    public $editingTaskDeadline = '';
    public $editingTaskStatus = '';
    public $editingTaskPriority = '';
    public $editingTaskDescription = '';

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->authorize('view', $project);
    }

    public function title()
    {
        return $this->project->title;
    }

    public function render()
    {
        $this->project->load(['owner', 'members']);

        $tasks = $this->project->tasks()
            ->search($this->search)
            ->filterStatus($this->task_status)
            ->with('assignee')
            ->orderBy('created_at')
            ->get();

        $projectUsers = collect([$this->project->owner])
            ->merge($this->project->members)
            ->unique('id')
            ->sortBy('name');

        $tenantId = auth()->user()->tenant_id ?? $this->project->tenant_id;
        $workflowStages = WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where(function ($query) {
                $query->whereNull('project_id')
                      ->orWhere('project_id', $this->project->id);
            })
            ->orderBy('sort_order')
            ->get();

        return view('livewire.project.board', [
            'tasks' => $tasks,
            'projectUsers' => $projectUsers,
            'workflowStages' => $workflowStages,
        ]);
    }

    public function addTask($stageKey, $stageId)
    {
        $this->authorize('updateTasks', $this->project);

        $this->validate([
            'newTaskTitle' => 'required|string|max:255',
        ]);

        $this->project->tasks()->create([
            'title' => $this->newTaskTitle,
            'assigned_to' => $this->newTaskAssignee ?: null,
            'deadline' => $this->newTaskDeadline ?: null,
            'priority' => $this->newTaskPriority ?: 'medium',
            'status' => $stageKey,
            'stage_id' => $stageId,
        ]);

        $this->reset(['newTaskTitle', 'newTaskAssignee', 'newTaskDeadline', 'newTaskPriority', 'addingTaskGroup']);
        $this->newTaskPriority = 'medium';
    }

    public function addSection()
    {
        $this->authorize('update', $this->project);

        $this->validate([
            'newSectionName' => 'required|string|max:255',
            'newSectionColor' => 'required|string',
            'newSectionIsActive' => 'boolean', // <-- TAMBAHAN: Validasi boolean
        ]);

        $tenantId = auth()->user()->tenant_id ?? $this->project->tenant_id;

        $maxOrder = WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->whereNull('project_id')
            ->max('sort_order') ?? 0;

        WorkflowStage::withoutGlobalScopes()->create([
            'tenant_id' => $tenantId,
            'project_id' => $this->project->id,
            'name' => $this->newSectionName,
            'key' => \Illuminate\Support\Str::slug($this->newSectionName, '_'),
            'color' => $this->newSectionColor,
            'is_active' => $this->newSectionIsActive ? true : false, // <-- TAMBAHAN: Simpan ke database
            'sort_order' => $maxOrder + 1,
        ]);

        // Reset form inputan termasuk checkbox
        $this->reset(['newSectionName', 'newSectionColor', 'newSectionIsActive']);
        $this->newSectionColor = '#6366f1';
        $this->dispatch('section-added');
    }

    public function selectTask(Task $task)
    {
        $this->selectedTaskId = $task->id;
        $this->editingTask = $task;
        $this->editingTaskTitle = $task->title;
        $this->editingTaskAssignee = $task->assigned_to;
        $this->editingTaskDeadline = $task->deadline?->format('Y-m-d');
        $this->editingTaskStatus = $task->status;
        $this->editingTaskPriority = $task->priority;
        $this->editingTaskDescription = $task->description;
    }

    public function closeSidebar()
    {
        $this->reset([
            'selectedTaskId', 'editingTask', 'editingTaskTitle', 'editingTaskAssignee',
            'editingTaskDeadline', 'editingTaskStatus', 'editingTaskPriority', 'editingTaskDescription'
        ]);
    }

    public function updateTask()
    {
        if (!$this->editingTask) return;
        $this->authorize('updateTasks', $this->project);

        $this->editingTask->update([
            'title' => $this->editingTaskTitle,
            'assigned_to' => $this->editingTaskAssignee ?: null,
            'deadline' => $this->editingTaskDeadline ?: null,
            'status' => $this->editingTaskStatus,
            'priority' => $this->editingTaskPriority,
            'description' => $this->editingTaskDescription,
        ]);

        $tenantId = auth()->user()->tenant_id ?? $this->project->tenant_id;
        $stage = WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->whereNull('project_id')
            ->where('key', $this->editingTaskStatus)
            ->first();

        if ($stage) {
            $this->editingTask->update(['stage_id' => $stage->id]);
        }

        session()->flash('success', 'Task updated.');
    }

    public function deleteTaskSection(WorkflowStage $stage)
    {
        $this->authorize('update', $this->project);

        $tenantId = $stage->tenant_id;
        $fallback = WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->whereNull('project_id')
            ->where('id', '!=', $stage->id)
            ->orderBy('sort_order')
            ->first();

        if ($fallback) {
            Task::withoutGlobalScopes()
                ->where('stage_id', $stage->id)
                ->update([
                    'stage_id' => $fallback->id,
                    'status' => $fallback->key,
                ]);
        }

        $stage->delete();
    }

    #[\Livewire\Attributes\On('taskMoved')]
    public function handleTaskMoved($taskId, $newStageId = null)
    {
        $this->authorize('updateTasks', $this->project);

        $task = Task::find($taskId);

        if ($task) {
            if ($newStageId) {
                $stage = WorkflowStage::withoutGlobalScopes()->find($newStageId);
                if ($stage) {
                    $task->update([
                        'stage_id' => $stage->id,
                        'status' => $stage->key,
                    ]);
                }
            } else {
                $task->update([
                    'stage_id' => null,
                    'status' => 'todo', // Default fallback status
                ]);
            }
        }
    }

    #[\Livewire\Attributes\On('sectionReordered')]
    public function handleSectionReordered($orderedIds)
    {
        $this->authorize('update', $this->project); // Only PIC/owner can reorder sections

        foreach ($orderedIds as $index => $stageId) {
            WorkflowStage::withoutGlobalScopes()
                ->where('id', $stageId)
                ->update(['sort_order' => $index + 1]);
        }
    }

    public function updateProjectStage($newStage)
    {
        $this->authorize('update', $this->project);

        if (!array_key_exists($newStage, Project::STAGES)) {
            return;
        }

        $this->project->update([
            'stage' => $newStage,
        ]);

        session()->flash('success', 'Stage proyek berhasil diperbarui.');
    }
}
