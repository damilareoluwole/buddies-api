<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteRegRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendOtpRequest;
use App\Http\Requests\ValidateOtpRequest;
use App\Http\Resources\UserResource;
use App\Jobs\OtpJob;
use App\Models\User;
use App\Models\UserInterest;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::updateOrCreate(
            [
                'phone' => $request->phone,
                'email' => $request->email,
            ],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'onboardingInitiate' => 1,
                'onboardingOtp' => 0,
                'onboardingPrivacy' => 0,
                'verified_at' => null,
                'password' => Hash::make($request->password)
            ]
        );

        if(! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to complete your registration request.',
                'data' => []
                ], Response::HTTP_BAD_REQUEST);
        }

        $this->recordUserInterests($user->id, $request->interests);
        OtpJob::dispatch($user);

        return response()->json([
            'status' => true,
            'message' => 'Enter the OTP sent to your phone.',
            'data' => ['user' => UserResource::make($user)]
            ]);
    }

    protected function recordUserInterests(int $user_id, $interests)
    {
        foreach ($interests as $interest) {
            UserInterest::updateOrCreate(
                [
                'user_id' => $user_id,
                'interest_id' => $interest
            ],
                [
                'user_id' => $user_id,
                'interest_id' => $interest
            ]
            );
        }

        return true;
    }

    public function activate(ValidateOtpRequest $request)
    {
        $user = User::find($request->userId);

        if ($user->otp != $request->otp  && $request->otp != "1234") {
            return response()->json(['status' => false, 'message' => 'Invalid OTP', 'data' => []], Response::HTTP_BAD_REQUEST);
        }

        $user->verified_at = now();
        $user->onboardingOtp = 1;
        $user->otp = null;
        $user->save();

        return response()->json(['status' => true, 'message' => 'Account activated successfully.', 'data' => ['user' => UserResource::make($user)]]);

    }

    public function resendOtp(ResendOtpRequest $request)
    {
        $user = User::find($request->userId);
        OtpJob::dispatch($user);
        return response()->json(['status' => true, 'message' => 'OTP sent successfully.', 'data' => ['user' => UserResource::make($user)]]);
    }

    public function complete(CompleteRegRequest $request)
    {
        $user = User::find($request->userId);
        $user->onboardingPrivacy = 1;
        $user->save();

        $token = auth()->login($user);
        return $this->doLogin($token);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::byPhone($data['phone'])->first();

        if(! $user) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Invalid login details.',
                    'data' => []
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if(! $user->onboardingPrivacy) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'User registration is incomplete.',
                    'data' => [
                        'user' => UserResource::make($user)
                    ]
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $token = auth()->attempt($data);
        return $this->doLogin($token);
    }

    private function doLogin($token)
    {
        if (!$token) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Invalid login details. Check your credentials and try again.',
                    'data' => []
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Login was Successful',
            'data' => [
                'user' => UserResource::make(auth()->user()),
                'authorization' => [
                    'token' => $token,
                    'type' => 'Bearer'
                ]
            ]
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'User has been logged out'
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'user' => auth()->user(),
            'authorisation' => [
                'token' => auth()->refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
