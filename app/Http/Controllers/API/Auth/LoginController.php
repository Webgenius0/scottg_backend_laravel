<?php

namespace App\Http\Controllers\API\Auth;

use Exception;
use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function Login(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // Check if email is valid
            if (filter_var($request->email, FILTER_VALIDATE_EMAIL) !== false) {
                $user = User::withTrashed()->where('email', $request->email)->first();
                if (empty($user)) {
                    return Helper::jsonErrorResponse('User not found', 404);
                }

<<<<<<< HEAD
                if ($user->email_verified_at) {
                    $user->is_verified = true;
                    $user->save();
                }
=======
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
>>>>>>> 6d8083fa8e0dd2279f7db1cb40c7d7b423c086b7
            }

            // Check the password
            if (!Hash::check($request->password, $user->password)) {
                return Helper::jsonErrorResponse('Invalid password', 401);
            }

            // Check if the email is verified before login is successful
            if (!$user->email_verified_at) {
                return Helper::jsonErrorResponse('Email not verified. Please verify your email before logging in.', 403);
            }

<<<<<<< HEAD
            // Generate token if email is verified and role matches
            $token = auth('api')->login($user);

            return response()->json([
                'status'     => true,
                'message'    => 'User logged in successfully.',
                'code'       => 200,
                'token_type' => 'bearer',
                'token'      => $token,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'data'       => auth('api')->user(),
            ], 200);
=======
            

            return ApiResponse::success('Logged in successfully', [
                'token_type' => 'Bearer',
                'token' => $token,
                'token_expires_in' => config('jwt.ttl') * 60,
                'user' => $user,
            ]);
>>>>>>> 6d8083fa8e0dd2279f7db1cb40c7d7b423c086b7
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }


    public function refreshToken(): \Illuminate\Http\JsonResponse
    {
<<<<<<< HEAD
        $refreshToken = auth('api')->refresh();

        return response()->json([
            'status'     => true,
            'message'    => 'Access token refreshed successfully.',
            'code'       => 200,
            'token_type' => 'bearer',
            'token'      => $refreshToken,
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'data' => auth('api')->user()->load('personalizedSickle')
        ]);
=======
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
>>>>>>> 6d8083fa8e0dd2279f7db1cb40c7d7b423c086b7
    }
}
