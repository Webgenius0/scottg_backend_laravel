<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use App\Helpers\ApiResponse;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{

    // Send OTP for password reset
    public function sendPasswordResetOtp(Request $request)
    {
        try {

            $request->validate([
                'email' => 'required|string|email',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return ApiResponse::error('User not found', 404);
            }

            // Generate and send OTP
            OtpService::generateAndSendOtp($user);

            return ApiResponse::success('OTP sent successfully');
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    // Verify OTP and reset password
    public function resetPassword(Request $request)
    {

        try {

            $request->validate([
                'email' => 'required|string|email',
                'otp' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $isVerified = OtpService::verifyOtp($request->email, $request->otp);

            if (!$isVerified) {
                return ApiResponse::error('Invalid OTP', 400);
            }

            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            return ApiResponse::success('Password reset successfully');
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
