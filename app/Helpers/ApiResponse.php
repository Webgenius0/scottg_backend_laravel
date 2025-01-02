<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($message, $data = [], $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'code' => $code,
            'data' => $data,
        ], $code);
    }

    public static function error($message, $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }
}

