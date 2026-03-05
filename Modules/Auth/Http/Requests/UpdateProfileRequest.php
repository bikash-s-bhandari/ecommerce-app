<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'current_password' => ['nullable', 'string'],
            'new_password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'required_with:current_password'
            ],
        ];
    }
}
