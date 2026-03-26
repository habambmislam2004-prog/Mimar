<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sender_business_account_id' => ['required', 'exists:business_accounts,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'details' => ['nullable', 'string'],
            'needed_at' => ['nullable', 'date'],
        ];
    }
}