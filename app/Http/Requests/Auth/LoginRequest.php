<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => __('validation.required', ['attribute' => 'login']),
            'password.required' => __('validation.required', ['attribute' => 'password']),
            'device_name.string' => __('validation.string', ['attribute' => 'device_name']),
        ];
    }
}