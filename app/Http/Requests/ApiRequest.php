<?php

namespace App\Http\Requests;

use App\Http\Controllers\Common\MessageReturnController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Common\MessageReturn;

abstract class ApiRequest extends FormRequest
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
            //
        ];
    }

    public function failedValidation(Validator $validator)
    {
        //$errors = (new ValidationException($validator))->errors();
        $errors = MessageReturnController::convertArrayMessage($validator->errors()->toArray());
        throw new HttpResponseException(response()->json(
            [
                'success' => false,
                'errors' => $errors,
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
