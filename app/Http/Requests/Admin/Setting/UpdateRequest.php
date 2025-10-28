<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $imageRule = ['sometimes', 'required', 'image', 'mimes:jpeg,png,jpg'];

        return [
            'admin-logo' => array_merge($imageRule, ['dimensions:min_width=135,min_height=45,max_width=160,max_height=65']),
            'site-title' => ['sometimes', 'required', 'max:20'],
            'site-subtitle' => ['sometimes', 'required', 'max:50'],
            'default-users-avatar' => array_merge($imageRule, ['dimensions:max_width=100,max_height=100']),
        ];
    }
}
