<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use App\Helpers\ApiResponse;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

    // Registration with OTP
    public function register(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'nullable|string',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'phone' => $validatedData['phone'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Generate and send OTP
            OtpService::generateAndSendOtp($user);

            return ApiResponse::success('User registered successfully, please verify your email.', [
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    // Resend OTP for registration
    public function resendRegistrationOtp(Request $request)
    {
        try {

            $request->validate([
                'email' => 'required|string|email',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return ApiResponse::error('User not found', 404);
            }

            if ($user->email_verified_at) {
                return ApiResponse::error('Email is already verified.', 400);
            }

            // Generate and send OTP
            OtpService::generateAndSendOtp($user);

            return ApiResponse::success('Registration OTP resent successfully.');
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    // Verify registration OTP
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|string',
        ]);

        try {

            $isVerified = OtpService::verifyOtp($request->email, $request->otp);

            if (!$isVerified) {
                return ApiResponse::error('Invalid OTP', 400);
            }

            $user = User::where('email', $request->email)->first();
            $user->email_verified_at = now();
            $user->save();

            return ApiResponse::success('Email verified successfully', [
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
