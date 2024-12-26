<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Backend\Auth\AuthController;

Route::middleware('api')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/api/register', 'register');
        Route::post('/api/login', 'login');
        Route::post('/api/verify-email', 'verifyEmail');
        Route::post('/api/resend-registration-otp', 'resendRegistrationOtp');
        Route::post('/api/password-reset-otp', 'sendPasswordResetOtp');
        Route::post('/api/password-reset', 'resetPassword');
        Route::middleware('auth:api')->group(function () {
            /* Route::get('/api/profile', 'profile');
            Route::post('/api/send-otp', 'sendOtp');
            Route::post('/api/verify-otp', 'verifyOTP');
            Route::post('/api/reset-password', 'resetPassword'); */
            Route::get('/api/refresh-token', 'refresh');
            Route::post('/api/logout', 'logout');
            
        });
    });
});
