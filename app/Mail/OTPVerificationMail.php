<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $otpCode;

    public function __construct(User $user, string $otpCode)
    {
        $this->user = $user;
        $this->otpCode = $otpCode;
    }

    public function build()
    {
        $userName = $this->user->name;
        $code = $this->otpCode;
        
        $html = "<html><body style='font-family: Arial; padding: 20px;'><div style='max-width: 600px; margin: 0 auto; background: white; padding: 40px;'><h1 style='color: #0081A7;'>OTP Verification</h1><p>Hello <strong>{$userName}</strong>,</p><p>Your OTP code is:</p><div style='background: #0081A7; color: white; font-size: 32px; font-weight: bold; text-align: center; padding: 20px; border-radius: 10px; letter-spacing: 10px;'>{$code}</div><p>This code will expire in 10 minutes.</p></div></body></html>";
        
        return $this->subject('Your OTP Verification Code')
                    ->html($html);
    }
}