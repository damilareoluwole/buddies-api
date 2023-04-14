<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'avatar' => ($this->avatar) ? env('APP_URL').Storage::url($this->avatar) : env('APP_URL').'/storage/images/users/default.jpg',
            'dob' => $this->dob,
            'onboardingInitiate' => $this->onboardingInitiate,
            'onboardingOtp' => $this->onboardingOtp,
            'onboardingPrivacy' => $this->onboardingPrivacy,
            'interest' => $this->interests,
        ];
    }
}