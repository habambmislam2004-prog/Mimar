<?php

namespace App\Http\Requests\BusinessAccount;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_activity_type_id' => ['required', 'exists:business_activity_types,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'license_number' => ['required', 'string', 'max:255', 'unique:business_accounts,license_number'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'activities' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'images' => ['nullable', 'array'],
            'images.*' => ['string', 'max:255'],

            'documents' => ['nullable', 'array'],
            'documents.*.file_name' => ['nullable', 'string', 'max:255'],
            'documents.*.file_path' => ['required_with:documents', 'string', 'max:255'],
            'documents.*.document_type' => ['nullable', 'string', 'max:100'],
        ];
    }
}