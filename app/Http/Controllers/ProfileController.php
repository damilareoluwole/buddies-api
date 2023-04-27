<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeEmailOtpRequest;
use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangePhoneOtpRequest;
use App\Http\Requests\ChangePhoneRequest;
use App\Http\Requests\EditProfileRequest;
use App\Jobs\OtpJob;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

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
                'user' => UserResource::make(User::find($request->user()->id))
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
        $user = $request->user();
        $user->phone = $request->phone;
        $user->save();

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
        $user = $request->user();
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Email successfully updated',
            'data' => [
                'user' => UserResource::make(User::find($request->user()->id))
            ]
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully',
            'data' => [
                'user' => UserResource::make(auth()->user())
            ]
        ]);
    }
}