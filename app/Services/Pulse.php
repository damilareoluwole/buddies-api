<?php

namespace App\Services;

use App\Services\ExternalApi;
use Illuminate\Support\Facades\Log;

class Pulse
{
    protected $message = ''; 
    protected $recipient = []; 
    
    public function message($message)
    {
        $this->message = $message;
        return $this;
    }

    public function recipient($recipient)
    {
        $this->recipient = $recipient;
        return $this;
    }
    
    public function sendMessage()
    {
        try{
            
            Log::info(config('pulse.url'));
            $response = (new ExternalApi)
                ->url(config('pulse.url'))
                ->method('post')
                ->extraHeaders($this->headers())
                ->body($this->preparePayload())
                ->process();
                
            if ($response['ResponseCode'] == "00") {
                return true;
            }  

            return false;
            
        }catch(\Throwable $th){
            Log::channel('sms')->info($th->getMessage()); 
            return false;       
        }

    }

    protected function preparePayload()
    {
        return [
            "Data" => [
                "Recipients"=> $this->recipient,
                "Params"=> [
                    "Code"=> "+234",
                    "Message"=> $this->message,
                    "SenderName" => "Readycash",
                ]
            ],
            "Name"=> "Aggregator.SMS"
        ];
    }

    protected function headers()
    {
        return [
            'pulse.origin' => 'ReadyCash.Default'
        ];
    }
}