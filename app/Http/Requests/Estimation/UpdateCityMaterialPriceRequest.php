<?php

namespace App\Http\Requests\Estimation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCityMaterialPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id' => ['required', 'exists:cities,id'],
            'material_type_id' => ['required', 'exists:material_types,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'effective_from' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}