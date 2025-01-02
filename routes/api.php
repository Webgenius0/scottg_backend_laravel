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

Route::middleware('api')->group(function () {

    //LoginController Routes
    Route::controller(LoginController::class)->group(function () {
        Route::post('/api/login', 'login');
        Route::middleware('auth:api')->group(function () {
            Route::get('/api/refresh-token', 'refresh');
        });
    });

    //RegisterController Routes
    Route::controller(RegisterController::class)->group(function () {
        Route::post('/api/register', 'register');
        Route::post('/api/verify-email', 'verifyEmail');
        Route::post('/api/resend-registration-otp', 'resendRegistrationOtp');
    });

    //ResetPasswordController Routes
    Route::controller(ResetPasswordController::class)->group(function () {
        Route::post('/api/password-reset-otp', 'sendPasswordResetOtp');
        Route::post('/api/password-reset', 'resetPassword');
    });

    //LogoutController Routes
    Route::controller(LogoutController::class)->group(function () {
        Route::middleware('auth:api')->group(function () {
            Route::post('/api/logout', 'logout');
        });
    });




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
        Route::middleware('auth:api')->group(function () {
            Route::get('/api/blogs', 'getActiveBlogs');
            Route::get('/api/blogs/{slug}', 'getBlogBySlug');
        });
    });

});


