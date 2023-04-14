<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
    Route::prefix('register')->group(function () {
        Route::post('/', [AuthController::class, 'register'])->name('jwt-auth.register');
        Route::post('/activate', [AuthController::class, 'activate'])->name('register.activate');
        Route::post('/complete', [AuthController::class, 'createPinAndAvatar'])->name('register.complete');
        Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('otp.resend');
    });
    
    Route::post('/login', [AuthController::class, 'login'])->name('jwt-auth.login');
});

Route::middleware('auth')->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('user.profile');
    });
});