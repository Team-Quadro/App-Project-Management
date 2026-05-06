<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRegistrationRequest extends FormRequest
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
            'company_name' => ['required', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:255'],
            'pic_phone' => ['nullable', 'string', 'max:50'],
            'pic_job_title' => ['nullable', 'string', 'max:255'],
            'estimated_users' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
