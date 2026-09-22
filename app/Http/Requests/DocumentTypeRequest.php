<?php

namespace App\Http\Requests;

use App\Models\Business;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('document_types', 'name')->ignore($this->route('document_type')),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'country_code' => ['nullable', Rule::in(array_keys(Business::COUNTRIES))],
            'is_required' => ['boolean'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
