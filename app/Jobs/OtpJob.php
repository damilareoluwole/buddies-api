<?php

namespace App\Jobs;

use App\Services\SMS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user; 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $code = (new SMS)->generateCode();
        $this->user->otp = $code;
        $this->user->save();

        $message = "To complete your request, use the OTP: $code";
        
        $sms = new SMS();
        $sms->users([$this->user]);
        $sms->message($message);
        $sms->sendSMS();
    }
    
}