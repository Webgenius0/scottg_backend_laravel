<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Exception;

class LoginController extends Controller
{

    // Login functionality
    public function login(Request $request)
    {
        try {

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
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
