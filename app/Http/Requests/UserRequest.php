<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends ApiRequest
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
            'email' => 'required|string|max:255|regex:/^[a-z0-9]+([\.\-_]?[a-z0-9]+)*@[a-z0-9]+([\.\-_]?[a-z0-9]+)*(\.[a-z0-9]{2,})+$/i|unique:users,email,' . $this->id,
            'password' => 'required|string|between:8,32|confirmed'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => config('messages.USER.NAME_IS_REQUIRED'),
            'name.between' => config('messages.USER.NAME_BETWEEN_5_20'),
            'email.required' => config('messages.USER.EMAIL_IS_REQUIRED'),
            'email' => config('messages.USER.EMAIL_REGEX'),
            'email.unique' => config('messages.USER.EMAIL_UNIQUE'),
            'password.required' => config('messages.USER.PASSWORD_IS_REQUIRED'),
            'password.between' => config('messages.USER.PASSWORD_BETWEEN_8_32'),
            'password.confirmed' => config('messages.USER.PASSWORD_IS_CONFIRMED'),
        ];
    }
}
