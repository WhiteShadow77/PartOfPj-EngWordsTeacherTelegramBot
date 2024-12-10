<?php

namespace App\Http\Requests;

use App\Services\ResponseService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateLanguageRequest extends FormRequest
{
    private ResponseService $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'language' => Rule::in(config('language.available_kinds'))
        ];
    }

    public function messages()
    {
        return [
          'language.in' => ucfirst($this->language . ' ' . __("language temporary unavailable"))
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->responseService->errorResponseWithExceptionAndKeyValueData($validator->errors()->all(), $validator->errors()->keys());
    }
}
