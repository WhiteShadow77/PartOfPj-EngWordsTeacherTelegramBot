<?php

namespace App\Http\Requests;

use App\Services\ResponseService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryItemRequest extends FormRequest
{
    private static $MAX_FILE_SIZE = 3 * 1024 * 1024;
    private ResponseService $responseService;
    private string $message;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }
    public function authorize()
    {
        return true;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $file = $this->file('image');
            if (!is_null($file) && $file->getSize() === 0) {
                $validator->errors()->add('image', 'Bad file');
                return;
            }
            if (!is_null($file) && $file->getSize() > self::$MAX_FILE_SIZE) {
                $validator->errors()->add('image', 'File is too big. Max size is ' . self::$MAX_FILE_SIZE . ' Bytes');
                return;
            }
        });
    }


    public function rules()
    {
        return [
            'name' => 'sometimes|string|min:5|max:50',
            'description' => 'sometimes|string|min:5|max:200',
            'image' => 'sometimes|file|mimes:jpeg,png',
        ];
    }

    public function messages()
    {
        return [
            'name.min' => 'Name must have minimum :min symbols',
            'name.max' => 'Name must have maximum :max symbols',
            'description.min' => 'Description must have minimum :min symbols',
            'description.max' => 'Description must have maximum :max symbols',
            'image.file' => 'Wrong data',
            'image.mimes' => 'Wrong file format. :values are required'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $message = $validator->errors()->first();
        $this->responseService->errorResponseWithException($message);
    }
}
