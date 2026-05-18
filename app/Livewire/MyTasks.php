<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Task;
use App\Models\WorkflowStage;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class MyTasks extends Component
{
    public string $search = '';
    public string $filterPriority = '';
    public string $filterStatus = '';

    // Sidebar task detail
    public ?int $selectedTaskId = null;
    public ?Task $editingTask = null;
    public string $editingTaskTitle = '';
    public string $editingTaskAssignee = '';
    public string $editingTaskDeadline = '';
    public string $editingTaskStatus = '';
    public string $editingTaskPriority = '';
    public string $editingTaskDescription = '';

    public function render()
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $tasks = Task::withoutGlobalScopes()
            ->where('assigned_to', $user->id)
            ->where('tenant_id', $tenantId)
            ->search($this->search)
            ->filterStatus($this->filterStatus)
            ->filterPriority($this->filterPriority)
            ->with(['assignee', 'project', 'stage'])
            ->orderBy('deadline')
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->get();

        $workflowStages = WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->whereNull('project_id')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.my-tasks', [
            'tasks'          => $tasks,
            'workflowStages' => $workflowStages,
        ]);
    }

    public function selectTask(Task $task)
    {
        if ($task->assigned_to !== Auth::id()) {
            return;
        }

        $this->selectedTaskId  = $task->id;
        $this->editingTask     = $task;
        $this->editingTaskTitle       = $task->title;
        $this->editingTaskAssignee    = (string) ($task->assigned_to ?? '');
        $this->editingTaskDeadline    = $task->deadline?->format('Y-m-d') ?? '';
        $this->editingTaskStatus      = $task->status ?? '';
        $this->editingTaskPriority    = $task->priority ?? '';
        $this->editingTaskDescription = $task->description ?? '';
    }

    public function closeSidebar()
    {
        $this->reset([
            'selectedTaskId', 'editingTask', 'editingTaskTitle', 'editingTaskAssignee',
            'editingTaskDeadline', 'editingTaskStatus', 'editingTaskPriority', 'editingTaskDescription',
        ]);
    }

    public function updateTask()
    {
        if (! $this->editingTask) {
            return;
        }

        $this->validate([
            'editingTaskTitle' => 'required|string|max:255',
        ]);

        $this->editingTask->update([
            'title'       => $this->editingTaskTitle,
            'deadline'    => $this->editingTaskDeadline ?: null,
            'status'      => $this->editingTaskStatus,
            'priority'    => $this->editingTaskPriority,
            'description' => $this->editingTaskDescription,
        ]);

        // Sync stage_id based on the chosen status key
        $tenantId = Auth::user()->tenant_id;
        $stage = WorkflowStage::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->whereNull('project_id')
            ->where('key', $this->editingTaskStatus)
            ->first();

        if ($stage) {
            $this->editingTask->update(['stage_id' => $stage->id]);
        }

        $this->dispatch('task-saved');
        session()->flash('success', 'Tugas berhasil diperbarui.');
    }
}
