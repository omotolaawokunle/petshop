<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'category_uuid' => 'required|string|exists:categories,uuid',
            'title' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'metadata' => 'required|array',
            'metadata.brand' => 'required|string|exists:brands,uuid',
            'metadata.image' => 'required|string|exists:files,uuid',
        ];
    }
}
