<?php

namespace App\Http\Requests;

use App\Models\Business;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Business $business */
        $business = $this->route('business');

        return [
            'document_type_id' => [
                'required',
                Rule::exists('document_types', 'id')->where(function ($query) use ($business): void {
                    $query->where('status', 'active')
                        ->where(function ($query) use ($business): void {
                            $query->whereNull('country_code')
                                ->orWhere('country_code', $business->country_code);
                        });
                }),
            ],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
