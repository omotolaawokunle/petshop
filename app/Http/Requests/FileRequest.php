<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
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
            'file' => 'required|image'
        ];
    }

    public function toArray(): array
    {
        return [
            'size' => number_format($this->file('file')->getSize() / 1024) . " KB",
            'name' => Str::random(),
            'type' => $this->file('file')->getMimeType(),
        ];
    }
}
