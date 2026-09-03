<?php

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Official\AnnouncementController;
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
use App\Http\Controllers\Official\RewardController as OfficialRewardController;
use App\Http\Controllers\SuperAdmin\SystemLogController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\ZoneController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $topResidents = \App\Models\User::where('role', 'resident')
        ->where('status', 'active')
        ->withSum('points', 'points')
        ->orderByDesc('points_sum_points')
        ->take(10)
        ->get(['id', 'name', 'address', 'zone_id'])
        ->filter(function ($user) {
            return $user->points_sum_points > 0;
        })
        ->map(function ($user) {
            return [
                'name' => $user->name,
                'address' => $user->address,
                'zone' => $user->zone ? $user->zone->name : 'Unknown',
                'points' => $user->points_sum_points ?? 0,
            ];
        })
        ->values();

    $stats = [
        [
            'label' => 'Registered Residents',
            'value' => number_format(\App\Models\User::where('role', 'resident')->where('status', 'active')->count())
        ],
        [
            'label' => 'Active Personnel',
            'value' => number_format(\App\Models\User::where('role', 'personnel')->where('status', 'active')->count())
        ],
        [
            'label' => 'Coverage Zones',
            'value' => number_format(\App\Models\Zone::count())
        ],
        [
            'label' => 'Completed Collections',
            'value' => number_format(\App\Models\CollectionTask::where('status', 'completed')->count())
        ],
    ];

    $announcements = \App\Models\Announcement::latest()->take(3)->get();

    return Inertia::render('Home', [
        'leaderboard' => $topResidents,
        'stats' => $stats,
        'announcements' => $announcements,
    ]);
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

        Route::get('system-logs', [SystemLogController::class, 'index'])->name('system-logs.index');
    });

    // Barangay Official routes
    Route::middleware('role:barangay_official')->prefix('official')->name('official.')->group(function () {
        Route::resource('schedules', ScheduleController::class)->except('show', 'create', 'edit');
        Route::resource('rewards', OfficialRewardController::class)->except('show', 'create', 'edit');
        Route::get('schedules/{schedule}/tasks', [ScheduleController::class, 'tasks'])->name('schedules.tasks');
        Route::post('tasks/{task}/reassign', [ScheduleController::class, 'reassignTask'])->name('tasks.reassign');
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/{report}', [ReportController::class, 'show'])->name('reports.show');
        Route::post('reports/{report}/respond', [ReportController::class, 'respond'])->name('reports.respond');
        Route::delete('reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
        Route::get('residents', [ResidentController::class, 'index'])->name('residents.index');
        Route::post('residents', [ResidentController::class, 'store'])->name('residents.store');
        Route::get('residents/{resident}', [ResidentController::class, 'show'])->name('residents.show');
        Route::post('residents/{resident}/approve', [ResidentController::class, 'approve'])->name('residents.approve');
        Route::post('residents/{resident}/reject', [ResidentController::class, 'reject'])->name('residents.reject');
        Route::post('residents/{resident}/deactivate', [ResidentController::class, 'deactivate'])->name('residents.deactivate');
        Route::post('residents/{resident}/reactivate', [ResidentController::class, 'reactivate'])->name('residents.reactivate');
        Route::delete('residents/{resident}', [ResidentController::class, 'destroy'])->name('residents.destroy');
        Route::get('redemptions', [RedemptionController::class, 'index'])->name('redemptions.index');
        Route::post('redemptions/{redemption}/approve', [RedemptionController::class, 'approve'])->name('redemptions.approve');
        Route::post('redemptions/{redemption}/reject', [RedemptionController::class, 'reject'])->name('redemptions.reject');
        
        Route::put('points/{point}', [\App\Http\Controllers\Official\PointController::class, 'update'])->name('points.update');

        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

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

// Background Queue & Schedule Worker Endpoint (for cron-job.org / Render)
Route::get('/run-background-jobs', function (\Illuminate\Http\Request $request) {
    $secret = config('app.cron_secret');

    // Reject if no secret configured or secret mismatch
    if (empty($secret) || $request->query('secret') !== $secret) {
        abort(403, 'Unauthorized');
    }

    // Execute scheduled commands (such as daily task generation and cleanup)
    \Illuminate\Support\Facades\Artisan::call('schedule:run');

    // Process queued jobs (stop when empty to avoid blocking the HTTP request)
    \Illuminate\Support\Facades\Artisan::call('queue:work', [
        '--stop-when-empty' => true,
        '--tries' => 3,
    ]);

    return response()->json([
        'status' => 'Jobs and scheduler executed successfully',
        'timestamp' => now()->toIso8601String(),
    ]);
});

