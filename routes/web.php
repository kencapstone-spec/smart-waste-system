<?php

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Official\PdfReportController;
use App\Http\Controllers\Official\RedemptionController;
use App\Http\Controllers\Official\ReportController;
use App\Http\Controllers\Official\ResidentController;
use App\Http\Controllers\Official\ScheduleController;
use App\Http\Controllers\Personnel\CollectionTaskController;
use App\Http\Controllers\Personnel\ScheduleController as PersonnelScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Resident\PointController;
use App\Http\Controllers\Resident\ReportController as ResidentReportController;
use App\Http\Controllers\Resident\RewardController as ResidentRewardController;
use App\Http\Controllers\Resident\ScheduleController as ResidentScheduleController;
use App\Http\Controllers\SuperAdmin\RewardController as SuperAdminRewardController;
use App\Http\Controllers\SuperAdmin\StreetController;
use App\Http\Controllers\SuperAdmin\SystemLogController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\ZoneController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [OtpController::class, 'showLoginForm'])->name('login');
    Route::post('/login/send-otp', [OtpController::class, 'sendOtp'])->middleware('throttle:3,1')->name('login.send-otp');
    Route::post('/login/verify-otp', [OtpController::class, 'verifyOtp'])->name('login.verify-otp');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [OtpController::class, 'logout'])->name('logout');

    // Super Admin routes
    Route::middleware('role:super_admin')->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('zones', ZoneController::class)->except('show', 'create', 'edit');
        Route::resource('streets', StreetController::class)->except('show', 'create', 'edit');
        Route::resource('rewards', SuperAdminRewardController::class)->except('show', 'create', 'edit');
        Route::get('system-logs', [SystemLogController::class, 'index'])->name('system-logs.index');
    });

    // Barangay Official routes
    Route::middleware('role:barangay_official')->prefix('official')->name('official.')->group(function () {
        Route::resource('schedules', ScheduleController::class)->except('show', 'create', 'edit');
        Route::get('schedules/{schedule}/tasks', [ScheduleController::class, 'tasks'])->name('schedules.tasks');
        Route::post('tasks/{task}/reassign', [ScheduleController::class, 'reassignTask'])->name('tasks.reassign');
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/{report}', [ReportController::class, 'show'])->name('reports.show');
        Route::post('reports/{report}/respond', [ReportController::class, 'respond'])->name('reports.respond');
        Route::get('residents', [ResidentController::class, 'index'])->name('residents.index');
        Route::get('residents/{resident}', [ResidentController::class, 'show'])->name('residents.show');
        Route::post('residents/{resident}/approve', [ResidentController::class, 'approve'])->name('residents.approve');
        Route::post('residents/{resident}/reject', [ResidentController::class, 'reject'])->name('residents.reject');
        Route::post('residents/{resident}/deactivate', [ResidentController::class, 'deactivate'])->name('residents.deactivate');
        Route::delete('residents/{resident}', [ResidentController::class, 'destroy'])->name('residents.destroy');
        Route::get('redemptions', [RedemptionController::class, 'index'])->name('redemptions.index');
        Route::post('redemptions/{redemption}/approve', [RedemptionController::class, 'approve'])->name('redemptions.approve');
        Route::post('redemptions/{redemption}/reject', [RedemptionController::class, 'reject'])->name('redemptions.reject');
        Route::get('pdf/collection-summary', [PdfReportController::class, 'collectionSummary'])->name('pdf.collection-summary');
        Route::get('pdf/complaints-summary', [PdfReportController::class, 'complaintsSummary'])->name('pdf.complaints-summary');
        Route::get('pdf/resident-participation', [PdfReportController::class, 'residentParticipation'])->name('pdf.resident-participation');
    });

    // Personnel routes
    Route::middleware('role:personnel')->prefix('personnel')->name('personnel.')->group(function () {
        Route::get('schedules', [PersonnelScheduleController::class, 'index'])->name('schedules.index');
        Route::get('tasks', [CollectionTaskController::class, 'index'])->name('tasks.index');
        Route::get('tasks/{task}', [CollectionTaskController::class, 'show'])->name('tasks.show');
        Route::get('tasks/{task}/residents', [CollectionTaskController::class, 'residents'])->name('tasks.residents');
        Route::post('tasks/{task}/update-status', [CollectionTaskController::class, 'updateStatus'])->name('tasks.update-status');
        Route::post('tasks/{task}/award-points', [CollectionTaskController::class, 'awardPoints'])->name('tasks.award-points');
    });

    // Resident routes
    Route::middleware('role:resident')->prefix('resident')->name('resident.')->group(function () {
        Route::get('schedules', [ResidentScheduleController::class, 'index'])->name('schedules.index');
        Route::get('reports', [ResidentReportController::class, 'index'])->name('reports.index');
        Route::post('reports', [ResidentReportController::class, 'store'])->name('reports.store');
        Route::get('points', [PointController::class, 'index'])->name('points.index');
        Route::get('rewards', [ResidentRewardController::class, 'index'])->name('rewards.index');
        Route::post('rewards/{reward}/redeem', [ResidentRewardController::class, 'redeem'])->name('rewards.redeem');
    });
});
