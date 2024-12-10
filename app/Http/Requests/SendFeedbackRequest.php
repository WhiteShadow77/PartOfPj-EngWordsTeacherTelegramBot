<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendFeedbackRequest extends FormRequest
{
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
            'contact_name' => 'required|min:3,max:15',
            'contact_email' => 'required|email:rfc,dns',
            'contact_message' => 'required|min:2,max:300',
        ];
    }

    public function messages()
    {
        return [
            'contact_name.required' => 'Name is required',
            'contact_name.min' => 'Name must have minimum :min symbols',
            'contact_name.max' => 'Name must have maximum :max symbols',
            'contact_email.required' => 'Email is required',
            'contact_email.email' => 'Wrong email format',
            'contact_message.required' => 'Letter is required',
            'contact_message.min' => 'Letter must have minimum :min symbols',
            'contact_message.max' => 'Letter must have maximum :max symbols',
        ];
    }
}
