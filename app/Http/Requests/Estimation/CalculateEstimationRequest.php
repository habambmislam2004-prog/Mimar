<?php

namespace App\Http\Requests\Estimation;

use Illuminate\Foundation\Http\FormRequest;

class CalculateEstimationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id' => ['required', 'exists:cities,id'],
            'estimation_type_id' => ['required', 'exists:estimation_types,id'],

            'length' => ['nullable', 'numeric', 'gt:0'],
            'width' => ['nullable', 'numeric', 'gt:0'],
            'height' => ['nullable', 'numeric', 'gt:0'],
            'thickness' => ['nullable', 'numeric', 'gt:0'],
            'area' => ['nullable', 'numeric', 'gt:0'],

            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}