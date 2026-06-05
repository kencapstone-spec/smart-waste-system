<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['street.zone', 'assignments.personnel', 'collectionTasks' => function ($q) {
            $q->where('personnel_id', Auth::id());
        }])
            ->whereHas('assignments', fn($q) => $q->where('personnel_id', Auth::id()))
            ->where('status', 'active')
            ->orderBy('start_date')
            ->get();

        return Inertia::render('Personnel/Schedules/Index', [
            'schedules' => $schedules,
        ]);
    }
}
