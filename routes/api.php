<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\BudgetController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\NetWorthController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\ResetPasswordController;

// Guest routes
Route::group(['middleware' => 'guest:api'], function () {
    // Register
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('/verify-email', [RegisterController::class, 'VerifyEmail']);
    Route::post('/resend-otp', [RegisterController::class, 'ResendOtp']);

    // Login
    Route::post('login', [LoginController::class, 'login']);

    // Forgot password
    Route::post('/forget-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/verify-otp', [ResetPasswordController::class, 'VerifyOTP']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
});

// Authenticated routes
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/refresh-token', [LoginController::class, 'refreshToken']);
    Route::post('/logout', [LogoutController::class, 'logout']);

    // CategoryController routes
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::post('/categories', 'store');
    });

    // BudgetController routes
    Route::controller(BudgetController::class)->group(function () {
        Route::get('/budgets', 'index');
        Route::post('/budgets', 'store');
        Route::put('/budgets/{id}', 'update');
        Route::delete('/budgets/{id}', 'destroy');
    });

    // TransactionController routes
    Route::controller(TransactionController::class)->group(function () {
        Route::get('/transactions', 'index');
        Route::post('/transactions', 'store');
        Route::put('/transactions/{id}', 'update');
        Route::delete('/transactions/{id}', 'destroy');
    });

    // BlogController routes
    Route::controller(BlogController::class)->group(function () {
        Route::get('/blogs', 'getActiveBlogs');
        Route::get('/blogs/{slug}', 'getBlogBySlug');
    });


    //NetWorthController routes
    Route::controller(NetWorthController::class)->group(function () {
        Route::get('/net-worth', 'getNetWorth');
    });

});

