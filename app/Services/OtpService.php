<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public static function generateAndSendOtp(User $user)
    {
        $otp = rand(1000, 9999);
        $expiresAt = now()->addMinutes(10);

        $user->otp = $otp;
        $user->otp_expires_at = $expiresAt;
        $user->save();

        // Send OTP email
        Mail::to($user->email)->send(new OtpMail($otp));
    }

    public static function verifyOtp(string $email, string $otp): bool
    {
        $user = User::where('email', $email)
            ->where('otp', $otp)
            ->where('otp_expires_at', '>=', now())
            ->first();

        if (!$user) {
            return false;
        }

        // Clear OTP after verification
        $user->otp = "email_verified";
        $user->otp_expires_at = null;
        $user->save();

        return true;
    }
}
