<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Only settings that already exist can be saved, so a tampered form
     * cannot create arbitrary keys. Money and duration settings are checked
     * because the fee and expiry logic silently treats bad values as zero.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $existingKeys = Setting::pluck('key')->implode(',');

        return [
            'settings' => ['required', 'array:'.$existingKeys],
            'settings.*' => ['nullable', 'string', 'max:2000'],
            'settings.certification_fee' => ['nullable', 'numeric', 'min:0'],
            'settings.renewal_fee' => ['nullable', 'numeric', 'min:0'],
            'settings.verification_validity_months' => ['nullable', 'integer', 'min:1', 'max:120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'settings.certification_fee' => 'certification fee',
            'settings.renewal_fee' => 'renewal fee',
            'settings.verification_validity_months' => 'verification validity (months)',
        ];
    }
}
