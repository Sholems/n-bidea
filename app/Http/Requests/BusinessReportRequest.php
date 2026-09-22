<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sector_id' => ['nullable', 'integer', 'exists:sectors,id'],
            'state' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in([
                'draft',
                'submitted',
                'under_review',
                'correction_required',
                'approved',
                'rejected',
                'verified',
                'expired',
                'suspended',
            ])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ];
    }
}
