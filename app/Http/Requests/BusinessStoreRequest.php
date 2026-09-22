<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'trading_name' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['required', 'string', 'max:100'],
            'business_type' => ['required', 'string', 'max:100'],
            'sector_id' => ['nullable', 'exists:sectors,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'address' => ['required', 'string', 'max:500'],
            'state' => ['required', 'string', 'max:100'],
            'lga' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email'],
            'website' => ['nullable', 'url', 'max:255'],
            'contact_person_name' => ['required', 'string', 'max:255'],
            'contact_person_phone' => ['required', 'string', 'max:20'],
            'contact_person_email' => ['required', 'email'],
            'trade_activity' => ['nullable', 'string', 'max:255'],
            'border_route' => ['nullable', 'string', 'max:255'],
            'cac_number' => ['nullable', 'string', 'max:100'],
            'nrs_number' => ['nullable', 'string', 'max:100'],
            'nin' => ['nullable', 'string', 'min:11', 'max:11'],
        ];
    }
}
