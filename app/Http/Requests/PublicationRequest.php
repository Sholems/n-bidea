<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class PublicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('is-super-admin') ?? false;
    }

    public function rules(): array
    {
        $publication = $this->route('publication');

        return [
            'content_category_id' => ['nullable', 'exists:content_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('publications', 'slug')->ignore($publication)],
            'description' => ['nullable', 'string', 'max:2000'],
            'file' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                File::types(['pdf'])->max('20mb'),
            ],
            'audience' => ['required', Rule::in(['public', 'members', 'internal'])],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
