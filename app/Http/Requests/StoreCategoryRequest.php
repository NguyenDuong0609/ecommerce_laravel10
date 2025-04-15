<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends ApiRequest
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
            'name' => 'required|string|max:55|unique:categories,name,' . $this->id,
            'slug' => 'required|string|max:55|unique:categories,slug,' . $this->id,
        ];
    }

    public function messages()
    {
        return [
            'name.required' => config('messages.CATEGORY.NAME_IS_REQUIRED'),
            'name.max' => config('messages.CATEGORY.NAME_MAX_55'),
            'name.unique' => config('messages.CATEGORY.NAME_UNIQUE'),
            'slug.required' => config('messages.CATEGORY.SLUG_IS_REQUIRED'),
            'slug.max' => config('messages.CATEGORY.SLUG_MAX_55'),
            'slug.unique' => config('messages.CATEGORY.SLUG_UNIQUE'),
        ];
    }
}
