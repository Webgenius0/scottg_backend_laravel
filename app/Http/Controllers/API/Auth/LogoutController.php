<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Exception;


class LogoutController extends Controller
{
<<<<<<< HEAD
    public function logout(): \Illuminate\Http\JsonResponse
    {
        try {
            if (Auth::check('api')) {
                Auth::logout('api');
                return Helper::jsonResponse(true, 'Logged out successfully. Token revoked.', 200);
            }

            return Helper::jsonErrorResponse( 'User not authenticated', 401);
=======
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return ApiResponse::success('Successfully logged out');
>>>>>>> 6d8083fa8e0dd2279f7db1cb40c7d7b423c086b7
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }
}
