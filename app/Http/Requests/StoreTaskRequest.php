<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(Task::STATUSES)],
            'stage_id' => ['nullable', 'exists:workflow_stages,id'],
            'priority' => ['required', Rule::in(Task::PRIORITIES)],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'deadline' => ['nullable', 'date'],
        ];
    }
}