<?php

namespace App\Http\Controllers;

use App\Jobs\OtpJob;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function initiate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email:dns|exists:users,email'
        ]);

        $user = User::where('email', $request->email);

        if(! $user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], Response::HTTP_BAD_REQUEST);
        }

        OtpJob::dispatch($user, $user->phone);

        return response()->json([
            'status' => true,
            'message' => 'OTP sent.'
        ]);
    }

    public function validateOtp(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email:dns|exists:users,email',
            'otp' => 'required|digits:4',
            'password' => 'required|confirmed'
        ]);

        $user = User::where('email', $request->email);

        if(! $user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], Response::HTTP_BAD_REQUEST);
        }

        if($request->otp != $user->otp) {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect OTP.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->otp = null;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
                'status' => true,
                'message' => 'Password reset successfully.'
            ]);
    }
}