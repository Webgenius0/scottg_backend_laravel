<?php

namespace App\Http\Controllers\Api\Backend\Auth;

use Exception;
use App\Models\User;
use App\Mail\OtpMail;
use App\Helpers\ApiResponse;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/* class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!$token = JWTAuth::attempt($request->only(['email', 'password']))) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'logged in successfully',
            'token' => $token
        ]);
    }

    public function register(Request $request)
    {

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // $token = JWTAuth::fromUser($user);

        OtpService::generateOTP($user);

        return ApiResponse::success('User registered successfully, please verify your email', [
            'user' => $user,
            // 'token' => $token
        ], 201);
    }

    public function profile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([

            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role

        ]);
    }


    //reset password by sending otp
    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $otp = rand(1000, 9999);
        $expiresAt = now()->addMinutes(10);

        try {
            Mail::to($user->email)->send(new OtpMail($otp));
            $user->otp = $otp;
            $user->otp_expires_at = $expiresAt;
            $user->save();

            return response()->json([
                'message' => 'OTP sent successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to send OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //reset password by verifying otp
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('otp_expires_at', '>=', now())
            ->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->otp != $request->otp || $user->otp_expires_at < now()) {
            return response()->json(['error' => 'Invalid OTP'], 401);
        }

        $user->otp = "email_verified";
        $user->otp_expires_at = null;
        $user->save();

        return response()->json([
            'message' => 'OTP verified successfully'
        ]);
    }

    //reset password by changing password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->otp != "email_verified") {
            return response()->json(['error' => 'Email not verified'], 401);
        }

        $user->password = Hash::make($request->password);
        $user->otp = 0;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return response()->json([
            'token' => JWTAuth::refresh(JWTAuth::getToken())
        ]);
    }
} */

class AuthController extends Controller
{
    // Login functionality
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponse::error('User not found', 404);
        }

        if (is_null($user->email_verified_at)) {
            return ApiResponse::error('Email not verified. Please verify your email first.', 403);
        }

        if (!$token = JWTAuth::attempt($credentials)) {
            return ApiResponse::error('Invalid credentials', 400);
        }

        return ApiResponse::success('Logged in successfully', [
            'token' => $token,
        ]);
    }

    // Registration with OTP
    public function register(Request $request)
    {
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
        ], 201);
    }

    // Resend OTP for registration
    public function resendRegistrationOtp(Request $request)
    {
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

        }
        catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // Send OTP for password reset
    public function sendPasswordResetOtp(Request $request)
    {
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
    }

    // Verify OTP and reset password
    public function resetPassword(Request $request)
    {
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
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return ApiResponse::success('Successfully logged out');
    }

    public function refresh()
    {
        return response()->json([
            'token' => JWTAuth::refresh(JWTAuth::getToken())
        ]);
    }

}
