<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            // النص اختياري
            'body' => 'nullable|string',

            // الملف اختياري
            'attachment' => 'nullable|file|max:20480',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            // لازم يكون يا نص يا ملف
            if (!$this->body && !$this->hasFile('attachment')) {

                $validator->errors()->add(
                    'body',
                    'يجب كتابة رسالة أو اختيار ملف'
                );
            }
        });
    }
}