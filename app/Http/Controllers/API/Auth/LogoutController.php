<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Exception;

class LogoutController extends Controller
{

    public function logout()
    {
        try {

            JWTAuth::invalidate(JWTAuth::getToken());
            return ApiResponse::success('Successfully logged out');
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function refresh()
    {

        try {

            return response()->json([
                'token' => JWTAuth::refresh(JWTAuth::getToken())
            ]);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
