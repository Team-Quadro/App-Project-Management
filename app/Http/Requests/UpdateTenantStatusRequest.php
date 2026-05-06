<?php

namespace App\Http\Requests;

use App\Models\Tenant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTenantStatusRequest extends FormRequest
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
            'status' => ['required', Rule::in(Tenant::STATUSES)],
            'approval_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
