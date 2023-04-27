<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PasswordValidationFailed extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Password Validation Failed, Please retry again'], Response::HTTP_PRECONDITION_FAILED);
    }
}