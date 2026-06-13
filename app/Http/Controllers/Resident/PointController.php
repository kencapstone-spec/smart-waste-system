<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Point;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PointController extends Controller
{
    public function index()
    {
        $points = Point::with(['awardedBy', 'collectionTask.schedule.zone'])
            ->where('resident_id', Auth::id())
            ->latest()
            ->get();

        return Inertia::render('Resident/Points/Index', [
            'points' => $points,
            'totalPoints' => $points->sum('points'),
        ]);
    }
}
