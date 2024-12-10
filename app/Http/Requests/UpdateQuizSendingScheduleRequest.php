<?php

namespace App\Http\Requests;

use App\Services\ResponseService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizSendingScheduleRequest extends FormRequest
{
    private ResponseService $responseService;
    private string $message;

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
            'days' => 'sometimes|array',
            'times' => 'sometimes|array',
            'quiz_quantities' => 'sometimes|array',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->responseService->errorResponseWithExceptionAndKeyValueData($validator->errors()->all(), $validator->errors()->keys());
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $days = $this->get('days');
            if (!is_null($days)) {
                $quizQuantities = $this->get('quiz_quantities');
                $times = $this->get('times');

                foreach ($days as $day) {
                    if (current($quizQuantities) == '') {
                        $validator->errors()->add(
                            'quiz' . $day . 'QuantitySelect',
                            'Quantity must not be empty'
                        );
                    } elseif (!is_numeric(current($quizQuantities))) {
                        $validator->errors()->add(
                            'quiz' . $day . 'QuantitySelect',
                            'Quantity must be a number'
                        );
                    }
                    if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', current($times))) {
                        $validator->errors()->add(
                            'quizSending' . $day . 'TimeSelect',
                            'Wrong time format'
                        );
                    }
                    next($quizQuantities);
                    next($times);
                }
            }
        });
    }
}
