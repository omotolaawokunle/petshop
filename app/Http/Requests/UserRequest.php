<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'avatar' => 'nullable|uuid|exists:avatars,uuid',
            'address' => 'required',
            'phone_number' => 'required|numeric',
            'is_marketing' => 'nullable'
        ];
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->get('first_name'),
            'last_name' => $this->get('last_name'),
            'email' => $this->get('email'),
            'password' => bcrypt($this->get('password')),
            'avatar' => $this->get('avatar'),
            'address' => $this->get('address'),
            'phone_number' => $this->get('phone_number'),
            'is_marketing' => ((bool) $this->get('is_marketing', 0)),
        ];
    }
}
