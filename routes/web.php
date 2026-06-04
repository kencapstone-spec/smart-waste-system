<?php

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\ZoneController;
use App\Http\Controllers\SuperAdmin\StreetController;
use App\Http\Controllers\SuperAdmin\SystemLogController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [OtpController::class, 'showLoginForm'])->name('login');
    Route::post('/login/send-otp', [OtpController::class, 'sendOtp'])->name('login.send-otp');
    Route::post('/login/verify-otp', [OtpController::class, 'verifyOtp'])->name('login.verify-otp');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::post('/logout', [OtpController::class, 'logout'])->name('logout');

    // Super Admin routes
    Route::middleware('role:super_admin')->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('zones', ZoneController::class)->except('show', 'create', 'edit');
        Route::resource('streets', StreetController::class)->except('show', 'create', 'edit');
        Route::get('system-logs', [SystemLogController::class, 'index'])->name('system-logs.index');
    });
});