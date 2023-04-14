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
    protected $phone;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $phone = null)
    {
        $this->user = $user; 
        $this->phone = $phone; 
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
        if($this->phone)
        {
            $sms->recipient[] = [
                "Value"=> (new SMS)->formatNumber($this->phone),
                "Type"=> 2,
                "Channel"=> 0
            ];
        }else{
            $sms->users([$this->user]);
        }
        
        $sms->message($message);
        $sms->sendSMS();
    }
    
}