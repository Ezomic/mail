<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\DevLoginController;
use App\Http\Controllers\Auth\LoginCodeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login/code', [LoginCodeController::class, 'send'])
        ->name('login.code.send')
        ->middleware('throttle:login');

    Route::get('login/code', [LoginCodeController::class, 'show'])
        ->name('login.code.challenge');

    Route::post('login/code/verify', [LoginCodeController::class, 'verify'])
        ->name('login.code.verify')
        ->middleware('throttle:login');

    if (app()->environment('local')) {
        Route::post('dev-login', [DevLoginController::class, 'store'])
            ->name('dev-login');
    }
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
