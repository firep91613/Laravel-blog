<?php

namespace App\Http\Requests\Admin\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:500'],
            'post_id' => ['required', 'int', 'exists:posts,id'],
            'user_id' => ['required', 'int', 'exists:users,id'],
            'parent_id' => ['nullable', 'exists:comments,id']
        ];
    }
}
