<?php
namespace App\Services;

use Illuminate\Support\Str;
use App\Services\Pulse;

Class SMS
{
    protected $users;
    protected $message;
    protected $recipient = [];

    public function users($users)
    {
        $this->users = $users;
        return $this;
    }

    public function message($message)
    {
        $this->message = $message;
        return $this;
    }
    
    public function sendSMS()
    {
        $this->process();
        
        $sendSMS = new Pulse();
        $sendSMS->message($this->message);
        $sendSMS->recipient($this->recipient);
        $sendSMS->sendMessage();
    }
    
    private function process()
    {
        foreach ($this->users as $user)
        {
            $this->recipient[] = [
                "Value"=> $this->formatNumber($user->phone),
                "Type"=> 2,
                "Channel"=> 0
            ];
        }

        $this->users = [];

        return;
    }

    private function formatNumber($phone)
    {
        return self::numberToInternationalFormat($phone);
    }

    public static function numberToInternationalFormat($phone){
        $nigerian = ["080", "081", "090", "070", "091", "+234", "234"];

        if(substr($phone, 0, 1) != '+' && in_array(substr($phone, 0, 3), $nigerian))
        {
            $phone = self::normalizePhone($phone);
        }

        return $phone;
    }

    public static function normalizePhone(string $phone, string $calling_code = '+234')
    {
        //Remove any parentheses and the number(s) with them
        $phone = preg_replace('/\([0-9]+?\)/', '', $phone);

        // Remove non-numeric characters
        $phone = preg_replace('/[^+0-9]/', '', $phone);

        // Remove leading zeros (if present, after calling code)
        $reg_exp = '/^(' . preg_quote($calling_code) . ')?(0)([0-9]+)$/';
        $phone = preg_replace_callback($reg_exp, function ($matches) use ($phone) {
            return empty($matches) ? $phone : $matches[1] . $matches[3];
        }, $phone);

        // Add calling code if phone does not already starts with the calling code
        if (!Str::startsWith($phone, $calling_code)) {
            $phone = $calling_code . $phone;
        }

        return $phone;
    }

    public function generateCode()
    {
        return rand(0000, 9999);
    }
}