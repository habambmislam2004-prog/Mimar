<?php

namespace App\Http\Requests\BusinessAccount;

use Illuminate\Foundation\Http\FormRequest;

class ApproveBusinessAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}