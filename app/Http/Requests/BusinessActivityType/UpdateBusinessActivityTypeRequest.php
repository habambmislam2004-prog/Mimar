<?php

namespace App\Http\Requests\BusinessActivityType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessActivityTypeRequest extends FormRequest
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
        $id = $this->route('business_activity_type')?->id ?? $this->route('business_activity_type');

        return [
            'name_ar' => ['required', 'string', 'max:255', Rule::unique('business_activity_types', 'name_ar')->ignore($id)],
            'name_en' => ['required', 'string', 'max:255', Rule::unique('business_activity_types', 'name_en')->ignore($id)],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
