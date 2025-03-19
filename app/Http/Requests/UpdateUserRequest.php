<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends ApiRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->id) // Ignore email của user đang cập nhật
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
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
