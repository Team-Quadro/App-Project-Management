<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya PIC (Admin perusahaan) yang boleh melakukan ini
        return $this->user()->isCompanyAdmin();
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'job_title' => ['required', 'string', 'max:255'],
        ];
    }
}