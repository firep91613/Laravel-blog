<?php

namespace App\Http\Requests\Public\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordStore extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:9|confirmed',
        ];
    }
}
