<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StoreProjectRequest extends FormRequest
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
            'status' => ['required', Rule::in(Project::STATUSES)],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'member_emails' => ['nullable', 'array'],
            'member_emails.*' => ['required', 'email'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $emails = $this->input('member_emails', []);

        if (is_string($emails)) {
            $emails = explode(',', $emails);
        }

        if (is_array($emails)) {
            $emails = collect($emails)
                ->map(fn ($email) => strtolower(trim($email)))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $this->merge([
            'member_emails' => $emails,
        ]);
    }

    public function messages(): array
    {
        return [
            'member_emails.*.email' => 'Each team member must be a valid email address.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $emails = $this->input('member_emails', []);

            if (! is_array($emails) || empty($emails)) {
                return;
            }

            $emails = array_values(array_unique(array_map('strtolower', $emails)));

            $userQuery = \App\Models\User::whereIn(DB::raw('LOWER(email)'), $emails);

            if (! $this->user()?->isSuperAdmin()) {
                $userQuery->where('tenant_id', $this->user()?->tenant_id);
            }

            $users = $userQuery
                ->pluck('email')
                ->map(fn ($email) => strtolower($email))
                ->all();

            $missing = array_diff($emails, $users);

            if (! empty($missing)) {
                $validator->errors()->add('member_emails', 'Some emails were not found in your subsidiary: ' . implode(', ', $missing));
            }
        });
    }
}