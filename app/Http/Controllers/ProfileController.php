<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeEmailOtpRequest;
use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangePhoneOtpRequest;
use App\Http\Requests\ChangePhoneRequest;
use App\Http\Requests\ConfirmPasswordRequest;
use App\Http\Requests\EditProfileRequest;
use App\Jobs\OtpJob;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Profile found',
            'data' => [
                'user' => UserResource::make(auth()->user())
            ]
        ]);
    }

    public function editProfile(EditProfileRequest $request)
    {
        User::where('id', $request->user()->id)->update(
            $request->validated()
        );

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => UserResource::make(auth()->user())
            ]
        ]);
    }

    public function confirmPassword(ConfirmPasswordRequest $request)
    {
        $user = $request->user();

        if (! Hash::check($request->validated()['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid password',
                'data' => []
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'status' => true,
            'message' => 'Password confirmed successfully',
            'data' => [
                'user' => UserResource::make($user)
            ]
        ]);
    }

    public function changePhone(ChangePhoneRequest $request)
    {
        OtpJob::dispatch($request->user(), $request->phone);

        return response()->json([
            'status' => true,
            'message' => 'Enter the OTP sent to your phone',
            'data' => [
                'phone' => $request->phone
            ]
        ]);
    }

    public function changeEmail(ChangeEmailRequest $request)
    {
        OtpJob::dispatch($request->user(), $request->user()->phone);

        return response()->json([
            'status' => true,
            'message' => 'Enter the OTP sent to your email',
            'data' => [
                'email' => $request->email
            ]
        ]);
    }

    public function changePhoneOtp(ChangePhoneOtpRequest $request)
    {
        if (! $this->validateOtp($request)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP',
                'data' => ['phone' => $request->phone]
            ], Response::HTTP_BAD_REQUEST);
        }

        User::where('id', $request->user()->id)->update(
            [
                'phone' => $request->phone
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Phone successfully updated',
            'data' => [
                'user' => UserResource::make(User::find($request->user()->id))
            ]
        ]);
    }

    public function changeEmailOtp(ChangeEmailOtpRequest $request)
    {
        if (! $this->validateOtp($request)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP',
                'data' => ['email' => $request->email]
            ], Response::HTTP_BAD_REQUEST);
        }

        User::where('id', $request->user()->id)->update(
            [
                'email' => $request->email
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Email successfully updated',
            'data' => [
                'user' => UserResource::make(User::find($request->user()->id))
            ]
        ]);
    }

    protected function validateOtp($request)
    {
        $user = $request->user();
        if ($user->otp != $request->otp) {
            return false;
        }

        $user->otp = null;
        $user->save();

        return true;

    }

    public function changePassword(ChangePasswordRequest $request)
    {
        User::where('id', $request->user()->id)->update(
            [
                'password' => Hash::make($request->password)
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully',
            'data' => [
                'user' => UserResource::make(auth()->user())
            ]
        ]);
    }
}