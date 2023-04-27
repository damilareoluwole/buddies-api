<?php

namespace App\Http\Requests;

use App\Rules\ValidateOtp;
use Illuminate\Foundation\Http\FormRequest;

class ChangePhoneOtpRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => [
                'required',
                'min:11',
                
            ],
            'otp' => [
                'required',
                'digits:4',
                new ValidateOtp()
            ]
        ];
    }
}