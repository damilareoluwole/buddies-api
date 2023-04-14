<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmPasswordRequest;
use App\Http\Requests\EditProfileRequest;
use App\Http\Requests\ProfileEditOtpRequest;
use App\Http\Requests\ValidateOtpRequest;
use Illuminate\Support\Facades\Hash;
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
        User::where('id', $request->user()->id)->update([
            $request->validated()
        ]);
        
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
        
        if (! Hash::check($request->validated()['password'], $user->password)) 
            return response()->json([
                'status' => false,
                'message' => 'Invalid password',
                'data' => []
            ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Password confirmed successfully',
            'data' => [
                'user' => UserResource::make($user)
            ]
        ]);
    }

    public function validateOtp(ProfileEditOtpRequest $request)
    {
        $user = $request->user();
        if ($user->otp != $request->otp)
            return response()->json(['status' => false, 'message' => 'Invalid OTP', 'data' => []], Response::HTTP_BAD_REQUEST);

        $user->otp = null;
        $user->save();

        return response()->json(['status' => true, 'message' => 'OTP verified successfully.', 'data' => ['user' => UserResource::make($user)]]);
        
    }

    public function changePhone(Request $request)
    {
        
    }

    public function changeEmail(Request $request){
        
    }

    public function changePassword(Request $request){
        
    }
}