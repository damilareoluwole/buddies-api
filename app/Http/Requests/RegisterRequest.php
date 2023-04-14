<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required','string'],
            'phone' => ['required',
                'regex:/\+?[0-9]{11}/',
                function ($attribute, $value, $fail) {
                    $user = User::where('phone', $value)
                                ->whereNotNull('verified_at')
                                ->first();
                    if ($user !== null) {
                        $fail('The ' . $attribute . ' has already been taken.');
                    }
                }
            ],
            'email' => [
                'email',
                'required',
                function ($attribute, $value, $fail) {
                    $user = User::where('email', $value)
                                ->whereNotNull('verified_at')
                                ->first();
                    if ($user !== null) {
                        $fail('The ' . $attribute . ' has already been taken.');
                    }
                }
            ],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'interests' => ['required', 'array'],
            'interests.*' => ['integer','exists:interests,id'],

        ];
    }
}