<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

     /**
     * Thêm ID từ URL vào input request trước khi validation chạy
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => [
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) {
                    return $query->where('id', $this->route('id'));
                }),
            ],
            'name' => ['required', 'string', 'max:55', 'unique:categories,name,' . $this->id],
            'parent_id' => ['nullable', 'integer'],
            'slug' => ['required', 'string', 'max:55', 'unique:categories,slug,' . $this->id],
        ];
    }

    public function messages(): array
    {
        return [
            'id.exists' => 'ID không tồn tại!',
            'name.required' => config('messages.CATEGORY.NAME_IS_REQUIRED'),
            'name.max' => config('messages.CATEGORY.NAME_MAX_55'),
            'name.unique' => config('messages.CATEGORY.NAME_UNIQUE'),
            'parent_id.integer' => config('messages.CATEGORY.PARENT_ID_IS_INTEGER'),
            'slug.required' => config('messages.CATEGORY.SLUG_IS_REQUIRED'),
            'slug.max' => config('messages.CATEGORY.SLUG_MAX_55'),
            'slug.unique' => config('messages.CATEGORY.SLUG_UNIQUE'),
        ];
    }
}
