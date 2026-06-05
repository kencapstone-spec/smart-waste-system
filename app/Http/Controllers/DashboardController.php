<?php

namespace App\Http\Controllers;

use App\Models\CollectionTask;
use App\Models\Point;
use App\Models\Report;
use App\Models\Schedule;
use App\Models\ScheduleAssignment;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stats = [];

        if ($user->isSuperAdmin()) {
            $stats = [
                'totalUsers'     => User::whereIn('role', ['barangay_official', 'personnel'])->count(),
                'totalResidents' => User::where('role', 'resident')->count(),
                'totalZones'     => Zone::count(),
            ];
        } elseif ($user->isAdmin()) {
            $stats = [
                'activeSchedules'  => Schedule::where('status', 'active')->count(),
                'pendingReports'   => Report::where('status', 'pending')->count(),
                'pendingResidents' => User::where('role', 'resident')->where('status', 'pending')->count(),
                'activeResidents'  => User::where('role', 'resident')->where('status', 'active')->count(),
            ];
        } elseif ($user->isPersonnel()) {
            $stats = [
                'assignedSchedules' => ScheduleAssignment::where('personnel_id', $user->id)->count(),
                'tasksToday'        => CollectionTask::where('personnel_id', $user->id)
                    ->whereDate('collection_date', today())
                    ->count(),
                'completedTasks'    => CollectionTask::where('personnel_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
            ];
        } elseif ($user->isResident()) {
            $stats = [
                'totalPoints'  => Point::where('resident_id', $user->id)->sum('points'),
                'totalReports' => Report::where('resident_id', $user->id)->count(),
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}
