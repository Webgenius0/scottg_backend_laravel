<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Backend\BudgetController;
use App\Http\Controllers\Api\Backend\CategoryController;
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


    //CategoryController routes
    Route::controller(CategoryController::class)->group(function () {
        Route::middleware('auth:api')->group(function () {
            Route::get('/api/categories', 'index');
            Route::post('/api/categories', 'store');
            // Route::get('/api/categories/{id}', 'show');
            // Route::put('/api/categories/{id}', 'update');
            // Route::delete('/api/categories/{id}', 'destroy');
        });
    });

    //BudgetController routes
    Route::controller(BudgetController::class)->group(function () {
        Route::middleware('auth:api')->group(function () {
            Route::get('/api/budgets', 'index');
            Route::post('/api/budgets', 'store');
            Route::put('/api/budgets/{id}', 'update');
            Route::delete('/api/budgets/{id}', 'destroy');
        });
    });
});
