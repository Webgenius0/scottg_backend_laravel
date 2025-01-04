<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\Backend\BlogController;
use App\Http\Controllers\Web\Backend\AdminController;
use App\Http\Controllers\Web\Backend\SystemSettingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//AdminController Routes
Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

//SystemSettingsController Routes
Route::get('/admin/system-settings', [SystemSettingController::class, 'index'])->name('admin.system-settings');
Route::post('/admin/system-settings', [SystemSettingController::class, 'update'])->name('admin.system-settings.update');
Route::get('/admin/mail-settings', [SystemSettingController::class, 'mailSetting'])->name('admin.mail-settings');
Route::post('/admin/mail-settings', [SystemSettingController::class, 'mailSettingUpdate'])->name('admin.mail-settings.update');
Route::get('/admin/profile', [SystemSettingController::class, 'profileIndex'])->name('admin.profile');
Route::post('/admin/profile', [SystemSettingController::class, 'profileUpdate'])->name('admin.profile.update');
Route::post('/admin/password', [SystemSettingController::class, 'passwordUpdate'])->name('admin.password.update');

//BlogController Routes
Route::get('/admin/blogs', [BlogController::class, 'index'])->name('admin.blogs');
Route::get('/admin/blogs/create', [BlogController::class, 'create'])->name('admin.blogs.create');
Route::post('/admin/blogs', [BlogController::class, 'store'])->name('admin.blogs.store');
Route::get('/admin/blogs/{id}/edit', [BlogController::class, 'edit'])->name('admin.blogs.edit');
Route::post('/admin/blogs/{id}', [BlogController::class, 'update'])->name('admin.blogs.update');
Route::delete('/admin/blogs/{id}', [BlogController::class, 'destroy'])->name('admin.blogs.destroy');
Route::get('/admin/status/{id}', [BlogController::class, 'changeStatus'])->name('admin.blogs.status');

require __DIR__.'/auth.php';
// require __DIR__.'/api.php';