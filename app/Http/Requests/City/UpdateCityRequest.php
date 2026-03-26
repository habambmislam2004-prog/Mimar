<?php

namespace App\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       $cityId = $this->route('city')?->id ?? $this->route('city');

        return [
            'name_ar' => ['required', 'string', 'max:255', Rule::unique('cities', 'name_ar')->ignore($cityId)],
            'name_en' => ['required', 'string', 'max:255', Rule::unique('cities', 'name_en')->ignore($cityId)],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
