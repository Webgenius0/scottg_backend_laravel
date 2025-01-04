<?php

namespace App\Http\Controllers\API\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpMail;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
<<<<<<< HEAD
    public function register(Request $request): \Illuminate\Http\JsonResponse
=======

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

            if (User::where('email', $validatedData['email'])->exists()) {
                return ApiResponse::error('Email already exists, please verify your email', 400);
            }

            $user = User::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Generate and send OTP
            OtpService::generateAndSendOtp($user);

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            return ApiResponse::success('User registered successfully, please verify your email.', [
                'token' => $token,
                'user' => $user->only('first_name', 'last_name', 'email', 'phone'),
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
>>>>>>> 6d8083fa8e0dd2279f7db1cb40c7d7b423c086b7
    {
        $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|string|email|max:150|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        try {
            $otp = random_int(10000, 99999);
            $otpExpiresAt = Carbon::now()->addMinutes(60);

            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'otp' => $otp,
                'otp_expires_at' => $otpExpiresAt,
                'is_otp_verified' => false,
            ]);

            // Send OTP email
            Mail::to($user->email)->send(new OtpMail($otp, $user, 'Verify Your Email Address'));
            return response()->json([
                'status' => true,
                'message' => 'User successfully registered. Please verify your email to log in.',
                'code' => 201,
                'data' => $user
            ], 201);

        } catch (Exception $e) {
            return Helper::jsonErrorResponse('User registration failed', 500, [$e->getMessage()]);
        }
    }

    public function VerifyEmail(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:5',
        ]);

        try {
            $user = User::where('email', $request->input('email'))->first();

<<<<<<< HEAD
            // Check if email has already been verified
            if (!empty($user->email_verified_at)) {
                return Helper::jsonErrorResponse('Email already verified.', 409);
            }

            // Check if OTP matches
            if ((string)$user->otp !== (string)$request->input('otp')) {
                return Helper::jsonErrorResponse('Invalid OTP code', 422);
            }

            // Check if OTP has expired
            if (Carbon::parse($user->otp_expires_at)->isPast()) {
                return Helper::jsonErrorResponse('OTP has expired. Please request a new OTP.', 422);
            }

            // Verify the email
            $user->email_verified_at = now();
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();
=======
            $user = User::where('email', $request->email)->first();

            if ($user->email_verified_at) {
                return ApiResponse::error('Email is already verified.', 400);
            }

            if (!$user) {
                return ApiResponse::error('User not found', 404);
            }

            $isVerified = OtpService::verifyOtp($request->email, $request->otp);

            if (!$isVerified) {
                return ApiResponse::error('Invalid OTP', 400);
            }

            $user->markEmailAsVerified();
>>>>>>> 6d8083fa8e0dd2279f7db1cb40c7d7b423c086b7

            // Generate the token
            $token = auth('api')->login($user);
            $tokenType = 'Bearer';

            // Return the response
            return response()->json([
                'status' => true,
                'message' => 'Email verification successful.',
                'code' => 200,
                'token' => $token,
                'token_type' => $tokenType,
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
          return Helper::jsonErrorResponse('User registration failed', 500, [$e->getMessage()]);
        }

    }


    public function ResendOtp(Request $request): ?\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        try {
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                return Helper::jsonErrorResponse('User not found.', 404);
            }

            if ($user->email_verified_at) {
                return Helper::jsonErrorResponse('Email already verified.', 409);
            }

            $newOtp = random_int(10000, 99999);
            $otpExpiresAt = Carbon::now()->addMinutes(60);
            $user->otp = $newOtp;
            $user->otp_expires_at = $otpExpiresAt;
            $user->save();
            Mail::to($user->email)->send(new OtpMail($newOtp, $user, 'Verify Your Email Address'));

            return Helper::jsonResponse(true, 'A new OTP has been sent to your email.', 200);
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), $e->getCode());
        }
    }

}
