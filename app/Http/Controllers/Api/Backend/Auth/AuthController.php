<?php

namespace App\Http\Controllers\Api\Backend\Auth;

use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
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

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token
        ]);
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
}
