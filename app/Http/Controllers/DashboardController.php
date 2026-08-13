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
                'totalStaff'     => User::whereIn('role', ['barangay_official', 'personnel'])->count(),
                'totalZones'     => Zone::count(),
            ];
        } elseif ($user->isAdmin()) {
            $bestResidentRecord = Point::selectRaw('resident_id, SUM(points) as total_points')
                ->groupBy('resident_id')
                ->orderByDesc('total_points')
                ->first();
            $bestResident = $bestResidentRecord ? User::find($bestResidentRecord->resident_id) : null;

            $bestZoneRecord = Schedule::join('collection_tasks', 'schedules.id', '=', 'collection_tasks.schedule_id')
                ->where('collection_tasks.status', 'completed')
                ->selectRaw('schedules.zone_id, COUNT(collection_tasks.id) as completed_tasks')
                ->groupBy('schedules.zone_id')
                ->orderByDesc('completed_tasks')
                ->first();
            $bestZone = $bestZoneRecord ? Zone::find($bestZoneRecord->zone_id) : null;

            $last7Days = collect(range(6, 0))->mapWithKeys(function ($i) {
                return [now()->subDays($i)->format('Y-m-d') => 0];
            });

            $tasksLast7Days = CollectionTask::where('status', 'completed')
                ->where('collection_date', '>=', now()->subDays(6)->format('Y-m-d'))
                ->selectRaw('collection_date, COUNT(*) as count')
                ->groupBy('collection_date')
                ->pluck('count', 'collection_date');

            $chartDates = $last7Days->merge($tasksLast7Days);

            $taskStatuses = CollectionTask::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            $stats = [
                'activeSchedules' => Schedule::where('status', 'active')->count(),
                'pendingReports' => Report::where('status', 'pending')->count(),
                'pendingResidents' => User::where('role', 'resident')->where('status', 'pending')->count(),
                'activeResidents' => User::where('role', 'resident')->where('status', 'active')->count(),
                'kpis' => [
                    'bestResident' => $bestResident ? $bestResident->name : 'No Data',
                    'bestResidentPoints' => $bestResidentRecord ? $bestResidentRecord->total_points : 0,
                    'bestZone' => $bestZone ? $bestZone->name : 'No Data',
                    'bestZoneTasks' => $bestZoneRecord ? $bestZoneRecord->completed_tasks : 0,
                ],
                'chartData' => [
                    'performance' => [
                        'labels' => $chartDates->keys()->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray(),
                        'data' => $chartDates->values()->toArray(),
                    ],
                    'statuses' => [
                        'completed' => $taskStatuses['completed'] ?? 0,
                        'pending' => $taskStatuses['pending'] ?? 0,
                        'missed' => $taskStatuses['missed'] ?? 0,
                    ]
                ]
            ];
        } elseif ($user->isPersonnel()) {
            $stats = [
                'assignedSchedules' => ScheduleAssignment::where('personnel_id', $user->id)->count(),
                'tasksToday' => CollectionTask::where('personnel_id', $user->id)
                    ->whereDate('collection_date', today())
                    ->count(),
                'completedTasks' => CollectionTask::where('personnel_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
            ];
        } elseif ($user->isResident()) {
            $stats = [
                'totalPoints' => Point::where('resident_id', $user->id)->sum('points'),
                'totalReports' => Report::where('resident_id', $user->id)->count(),
            ];
        }

        $announcements = \App\Models\Announcement::latest()->take(3)->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'announcements' => $announcements,
        ]);
    }
}
