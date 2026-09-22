<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserManageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['super_admin', 'admin', 'business_owner', 'government_official'])],
            'account_status' => ['required', Rule::in(['active', 'suspended', 'pending'])],
            'agency_id' => ['nullable', 'exists:agencies,id'],
        ];
    }
}
