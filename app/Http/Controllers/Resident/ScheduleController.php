<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $schedules = Schedule::with(['zone', 'assignments.personnel'])
            ->where('zone_id', $user->zone_id)
            ->where('status', 'active')
            ->orderBy('start_date')
            ->get();

        return Inertia::render('Resident/Schedules/Index', [
            'schedules' => $schedules,
            'myZone' => $user->load('zone')->zone,
        ]);
    }
}
