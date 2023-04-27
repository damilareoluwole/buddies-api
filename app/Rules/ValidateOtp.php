<?php

namespace App\Rules;

use App\Services\OtpService;
use Illuminate\Contracts\Validation\Rule;

class ValidateOtp implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $service =  new OtpService(auth()->user());
            return $service->validate($value);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Incorrect otp.';
    }
}