<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InterestController;
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

Route::prefix('interests')->group(function () {
    Route::get('/', [InterestController::class, 'index'])->name('interests.index');
});

Route::prefix('auth')->group(function () {
    Route::prefix('register')->group(function () {
        Route::post('/', [AuthController::class, 'register'])->name('jwt-auth.register');
        Route::post('/activate', [AuthController::class, 'activate'])->name('register.activate');
        Route::post('/complete', [AuthController::class, 'complete'])->name('register.complete');
        Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('otp.resend');
    });

    Route::post('/login', [AuthController::class, 'login'])->name('jwt-auth.login');
});

Route::middleware('auth')->group(function () {
    Route::prefix('user')->group(function () {
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('user.profile');
            Route::post('/edit', [ProfileController::class, 'editProfile'])->name('user.edit.profile');
        });

        Route::post('/confirm-password', [ProfileController::class, 'confirmPassword'])->name('user.confirm-password');

        Route::prefix('change')->group(function () {
            Route::post('/password', [ProfileController::class, 'changePassword'])->name('change.changePassword');
            Route::prefix('phone')->group(function () {
                Route::post('/', [ProfileController::class, 'changePhone'])->name('change.phone');
                Route::post('/complete', [ProfileController::class, 'changePhoneOtp'])->name('change.phone.complete');
            });
            Route::prefix('email')->group(function () {
                Route::post('/', [ProfileController::class, 'changeEmail'])->name('change.email');
                Route::post('/complete', [ProfileController::class, 'changeEmailOtp'])->name('change.email.complete');
            });
        });
    });

});