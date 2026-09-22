<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('is-super-admin') ?? false;
    }

    public function rules(): array
    {
        $post = $this->route('post');

        return [
            'content_category_id' => ['nullable', 'exists:content_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($post)],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'body' => ['required', 'string'],
            'featured_image_path' => ['nullable', 'string', 'max:255'],
            'audience' => ['required', Rule::in(['public', 'members', 'internal'])],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
