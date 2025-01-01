<?php

namespace App\Http\Controllers\API\Auth;

use Exception;
use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
            $user = User::where('email', $request->email)
            ->select('first_name', 'last_name', 'email', 'phone', 'email_verified_at')
            ->first();

            if($user->role == 'admin'){
                return redirect()->route('admin.dashboard');
            }

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
                'token_type' => 'Bearer',
                'token' => $token,
                'token_expires_in' => config('jwt.ttl') * 60,
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function refresh()
    {
        try {
            $currentToken = JWTAuth::getToken();

            if (!$currentToken) {
                return ApiResponse::error('No token provided', 400);
            }

            $token = JWTAuth::refresh($currentToken);
            $user = auth()->user();

            if (!$user) {
                return ApiResponse::error('User not found', 404);
            }

            return ApiResponse::success('Token refreshed successfully', [
                'token_type' => 'Bearer',
                'token' => $token,
                'token_expires_in' => config('jwt.ttl') * 60,
                'user' => $user->only('first_name', 'last_name', 'email', 'phone', 'email_verified_at'),
            ]);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

}
