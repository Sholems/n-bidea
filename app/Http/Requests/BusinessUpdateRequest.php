<?php

namespace App\Http\Requests;

use App\Models\Business;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => ['sometimes', 'string', 'max:255'],
            'trading_name' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['sometimes', 'string', 'max:100'],
            'business_type' => ['sometimes', 'string', 'max:100'],
            'country_code' => ['sometimes', Rule::in(array_keys(Business::COUNTRIES))],
            'sector_id' => ['nullable', 'exists:sectors,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'address' => ['sometimes', 'string', 'max:500'],
            'state' => ['sometimes', 'string', 'max:100'],
            'lga' => ['nullable', 'string', 'max:100'],
            'city' => ['sometimes', 'string', 'max:100'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'email' => ['sometimes', 'email'],
            'website' => ['nullable', 'url', 'max:255'],
            'contact_person_name' => ['sometimes', 'string', 'max:255'],
            'contact_person_phone' => ['sometimes', 'string', 'max:20'],
            'contact_person_email' => ['sometimes', 'email'],
            'trade_activity' => ['nullable', 'string', 'max:255'],
            'border_route' => ['nullable', 'string', 'max:255'],
            'cac_number' => ['nullable', 'string', 'max:100'],
            'nrs_number' => ['nullable', 'string', 'max:100'],
            'nin' => ['nullable', 'string', 'min:11', 'max:11'],
        ];
    }
}
