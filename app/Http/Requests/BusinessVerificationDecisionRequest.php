<?php

namespace App\Http\Requests;

use App\Models\Business;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessVerificationDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $business = $this->route('business');

        return $business instanceof Business
            && ($this->user()?->can('verify', $business) ?? false);
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in(['verified', 'not_verified'])],
            'method' => ['required', Rule::in(['phone_call', 'site_visit'])],
            'note' => ['required', 'string', 'min:10', 'max:1500'],
        ];
    }
}
