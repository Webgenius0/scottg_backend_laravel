<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Exception;


class LogoutController extends Controller
{
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return ApiResponse::success('Successfully logged out');
        } catch (Exception $e) {
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }
}
