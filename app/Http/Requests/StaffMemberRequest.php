<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'job_title' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'nationality' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'nin' => ['nullable', 'digits:11'],
            'passport_number' => ['nullable', 'string', 'max:50'],
        ];
    }
}
