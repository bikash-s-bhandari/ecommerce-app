<?php

namespace Modules\Catalog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadProductImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'images'           => ['required', 'array', 'min:1', 'max:10'],
            'images.*'         => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'primary_index'    => ['nullable', 'integer', 'min:0'],
        ];
    }
}
