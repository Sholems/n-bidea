<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeeUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proof_file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
        ];
    }
}
