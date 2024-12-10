<?php

namespace App\Http\Requests;

use App\Services\ResponseService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CvUpdateRequest extends FormRequest
{
    private static $MAX_FILE_SIZE = 2 * 1024 * 1024;
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
            $file = $this->file('cv');
            if ($file === null || $file->getSize() === 0) {
                $validator->errors()->add('cv', 'Bad file');
                return;
            }
            if ($file->getSize() > self::$MAX_FILE_SIZE) {
                $validator->errors()->add('cv', 'File is too big. Max size is ' . self::$MAX_FILE_SIZE . ' Bytes');
                return;
            }
        });
    }


    public function rules()
    {
        return [
            'cv' => 'required|file|mimes:pdf',
        ];
    }

    public function messages()
    {
        return [
            'cv.required' => 'File is required',
            'cv.file' => 'Wrong data',
            'cv.mimes' => 'Wrong file format. PDF is required'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $message = $validator->errors()->first();
        $this->responseService->errorResponseWithException($message);
    }
}
