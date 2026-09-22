<?php

namespace App\Http\Requests;

use App\Models\BusinessProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $businessProfile = $this->route('businessProfile');

        return $this->user() !== null
            && $businessProfile instanceof BusinessProfile
            && $businessProfile->status === 'approved';
    }

    public function rules(): array
    {
        return [
            'requester_company' => ['nullable', 'string', 'max:255'],
            'requester_phone' => ['nullable', 'string', 'max:30'],
            'interest_type' => ['required', Rule::in(['buyer', 'supplier', 'distributor', 'investor', 'service_provider', 'other'])],
            'message' => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }
}
