<?php

namespace App\Http\Requests\Admin\Category;

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
            'name' => ['required', 'string', 'max:50', 'unique:categories,name'],
            'description' => ['required', 'string', 'max:100', 'unique:categories,description'],
            'slug' => ['required', 'string', 'max:50', 'unique:categories,slug'],
        ];
    }
}
