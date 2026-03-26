<?php

namespace App\Http\Requests\Web\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterWebRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'account_type' => ['required', 'in:user,business'], 
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('messages.attributes.name'),
            'email' => __('messages.attributes.email'),
            'password' => __('messages.attributes.password'),
            'password_confirmation' => __('messages.attributes.password_confirmation'),
            'account_type' => __('messages.attributes.account_type'),
        ];
    }
}