<?php

namespace App\Services;

use App\Exceptions\PasswordValidationFailed;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

class PasswordService
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
    public function match(string $password)
    {
        return Hash::check($password, $this->user->password);
    }

    /**
     * @throws PasswordValidationFailed
     */
    public function validate(string $password)
    {
        //ensure max attempt isn't three
        if ($this->match($password)) {
            return true;
        }

        throw new PasswordValidationFailed;
    }
}