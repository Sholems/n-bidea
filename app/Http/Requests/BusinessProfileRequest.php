<?php

namespace App\Http\Requests;

use App\Models\Business;
use Illuminate\Foundation\Http\FormRequest;

class BusinessProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $business = $this->route('business');

        return $business instanceof Business
            && $this->user()?->id === $business->user_id
            && in_array($business->status, ['approved', 'verified']);
    }

    public function rules(): array
    {
        return [
            'summary' => ['required', 'string', 'min:20', 'max:1200'],
            'services' => ['required', 'string', 'min:10', 'max:1200'],
            'operating_locations' => ['required', 'string', 'min:3', 'max:500'],
            'trade_interests' => ['nullable', 'string', 'max:1200'],
            'certifications' => ['nullable', 'string', 'max:700'],
            'website' => ['nullable', 'url', 'max:255'],
            'contact_preference' => ['nullable', 'string', 'max:500'],
        ];
    }
}
