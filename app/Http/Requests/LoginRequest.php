<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends ApiRequest
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
        return [
            'email' => 'required|string|max:255,email|regex:/^[a-z0-9]+([\.\-_]?[a-z0-9]+)*@[a-z0-9]+([\.\-_]?[a-z0-9]+)*(\.[a-z0-9]{2,})+$/i',
            'password' => 'required|string|between:8,32'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => config('messages.SIGNUP.EMAIL_IS_REQUIRED'),
            'email.email' => config('messages.SIGNUP.EMAIL_REGEX'),
            'password.required' => config('messages.SIGNUP.PASSWORD_IS_REQUIRED'),
            'password.between' => config('messages.SIGNUP.PASSWORD_BETWEEN_8_32'),
        ];
    }
}
