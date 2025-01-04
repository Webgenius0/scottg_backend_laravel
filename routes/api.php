<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\BudgetController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\ResetPasswordController;


Route::group(['middleware' => 'guest:api'], static function () {
    //register
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('/verify-email', [RegisterController::class, 'VerifyEmail']);
    Route::post('/resend-otp', [RegisterController::class, 'ResendOtp']);
    //login
    Route::post('login', [LoginController::class, 'login']);
    //forgot password
    Route::post('/forget-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/verify-otp', [ResetPasswordController::class, 'VerifyOTP']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
    //social login
    //    Route::post('/social-login', [SocialLoginController::class, 'SocialLogin']);
});


Route::group(['middleware' => 'auth:api'], static function () {
    Route::get('/refresh-token', [LoginController::class, 'refreshToken']);
    Route::post('/logout', [LogoutController::class, 'logout']);

    //CategoryController routes
    Route::controller(CategoryController::class)->group(function () {
        Route::middleware('auth:api')->group(function () {
            Route::get('/api/categories', 'index');
            Route::post('/api/categories', 'store');
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

    //TransactionController routes
    Route::controller(TransactionController::class)->group(function () {
        Route::middleware('auth:api')->group(function () {
            Route::get('/api/transactions', 'index');
            Route::post('/api/transactions', 'store');
            Route::put('/api/transactions/{id}', 'update');
            Route::delete('/api/transactions/{id}', 'destroy');
        });
    });

    //BlogController routes
    Route::controller(BlogController::class)->group(function () {
        Route::get('/api/blogs', 'getActiveBlogs');
        Route::get('/api/blogs/{slug}', 'getBlogBySlug');
    });
});
