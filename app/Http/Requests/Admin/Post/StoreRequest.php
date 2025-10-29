<?php

namespace App\Http\Requests\Admin\Post;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'slug' => ['required', 'string', 'max:50', 'unique:posts'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'int', 'exists:categories,id'],
            'image' => ['nullable', 'image', 'dimensions:min_width=700,min_height=400', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'user_id' => ['required', 'int', 'exists:users,id'],
            'tag_id' => ['nullable', 'array'],
        ];
    }
}
