<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Resolve valid statuses from the project's tenant stages
        $project = $this->route('project');
        $validStatuses = Task::getValidStatuses();

        if ($project && $project->tenant_id) {
            $stageKeys = \App\Models\WorkflowStage::withoutGlobalScopes()
                ->where('tenant_id', $project->tenant_id)
                ->whereNull('project_id')
                ->pluck('key')
                ->toArray();
            if (!empty($stageKeys)) {
                $validStatuses = $stageKeys;
            }
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in($validStatuses)],
            'stage_id' => ['nullable', 'exists:workflow_stages,id'],
            'priority' => ['required', Rule::in(Task::PRIORITIES)],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'deadline' => ['nullable', 'date'],
        ];
    }
}