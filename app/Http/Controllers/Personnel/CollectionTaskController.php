<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\CollectionTask;
use App\Models\Point;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CollectionTaskController extends Controller
{
    public function index()
    {
        $tasks = CollectionTask::with(['schedule.zone', 'photos'])
            ->where('personnel_id', Auth::id())
            ->orderBy('collection_date')
            ->paginate(15);

        return Inertia::render('Personnel/CollectionTasks/Index', [
            'tasks' => $tasks,
        ]);
    }

    public function show(CollectionTask $task)
    {
        // Ensure this task belongs to the authenticated personnel
        abort_if($task->personnel_id !== Auth::id(), 403);

        $residents = User::with('points')
            ->where('role', 'resident')
            ->where('status', 'active')
            ->where('zone_id', $task->schedule->zone_id)
            ->get()
            ->map(function ($resident) use ($task) {
                $resident->task_points = $resident->points
                    ->where('collection_task_id', $task->id)
                    ->sum('points');

                return $resident;
            });

        return Inertia::render('Personnel/CollectionTasks/Show', [
            'task' => $task->load(['schedule.zone', 'photos']),
            'residents' => $residents,
        ]);
    }

    public function residents(CollectionTask $task)
    {
        abort_if($task->personnel_id !== Auth::id(), 403);

        $residents = User::with('points')
            ->where('role', 'resident')
            ->where('status', 'active')
            ->whereHas('zone', fn ($q) => $q->where('id', $task->schedule->zone_id))
            ->get()
            ->map(function ($resident) use ($task) {
                $resident->task_points = $resident->points
                    ->where('collection_task_id', $task->id)
                    ->sum('points');

                return $resident;
            });

        return response()->json($residents);
    }

    public function updateStatus(Request $request, CollectionTask $task)
    {
        abort_if($task->personnel_id !== Auth::id(), 403);

        $request->validate([
            'status' => ['required', 'in:completed,missed,pending'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'max:5120'],
        ]);

        $task->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('collection-tasks', 'public');
                $task->photos()->create(['photo_path' => $path]);
            }
        }

        return back()->with('success', 'Task status updated successfully.');
    }

    public function awardPoints(Request $request, CollectionTask $task)
    {
        abort_if($task->personnel_id !== Auth::id(), 403);

        $request->validate([
            'resident_id' => ['required', 'exists:users,id'],
            'points' => ['nullable', 'integer', 'min:1', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $points = $request->filled('points') ? (int) $request->points : Point::FIXED_AWARD_POINTS;

        Point::create([
            'resident_id' => $request->resident_id,
            'awarded_by' => Auth::id(),
            'collection_task_id' => $task->id,
            'points' => $points,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', "{$points} points awarded successfully.");
    }
}
