<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignUpUserRequest extends ApiRequest
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
            'name' => 'required|string|between:5,20',
            'email' => 'required|string|max:255|unique:users,email|regex:/^[a-z0-9]+([\.\-_]?[a-z0-9]+)*@[a-z0-9]+([\.\-_]?[a-z0-9]+)*(\.[a-z0-9]{2,})+$/i',
            'password' => 'required|string|between:8,32|confirmed'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => config('messages.SIGNUP.NAME_IS_REQUIRED'),
            'name.between' => config('messages.SIGNUP.NAME_BETWEEN_5_20'),
            'email.required' => config('messages.SIGNUP.EMAIL_IS_REQUIRED'),
            'email' => config('messages.SIGNUP.EMAIL_REGEX'),
            'email.unique' => config('messages.SIGNUP.EMAIL_UNIQUE'),
            'password.required' => config('messages.SIGNUP.PASSWORD_IS_REQUIRED'),
            'password.between' => config('messages.SIGNUP.PASSWORD_BETWEEN_8_32'),
            'password.confirmed' => config('messages.SIGNUP.PASSWORD_IS_CONFIRMED'),
        ];
    }
}
