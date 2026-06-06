<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use App\Models\CollectionTask;
use App\Models\Schedule;
use App\Models\Street;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['street.zone', 'createdBy', 'assignments.personnel'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Official/Schedules/Index', [
            'schedules' => $schedules,
            'streets' => Street::with('zone')->orderBy('name')->get(['id', 'name', 'zone_id']),
            'personnel' => User::where('role', 'personnel')
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'street_id' => ['required', 'exists:streets,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'frequency' => ['required', 'in:once,daily,weekly,monthly'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'collection_time' => ['required'],
            'personnel_ids' => ['required', 'array', 'min:1'],
            'personnel_ids.*' => ['exists:users,id'],
        ]);

        $schedule = Schedule::create([
            'street_id' => $request->street_id,
            'created_by' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'frequency' => $request->frequency,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'collection_time' => $request->collection_time,
            'status' => 'active',
        ]);

        foreach ($request->personnel_ids as $personnelId) {
            $schedule->assignments()->create(['personnel_id' => $personnelId]);
        }

        // Auto-generate collection tasks
        $this->generateCollectionTasks($schedule, $request->personnel_ids);

        return back()->with('success', 'Schedule created successfully.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'street_id' => ['required', 'exists:streets,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'frequency' => ['required', 'in:once,daily,weekly,monthly'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'collection_time' => ['required'],
            'status' => ['required', 'in:active,inactive'],
            'personnel_ids' => ['required', 'array', 'min:1'],
            'personnel_ids.*' => ['exists:users,id'],
        ]);

        $schedule->update($request->only(
            'street_id', 'title', 'description', 'frequency',
            'start_date', 'end_date', 'collection_time', 'status'
        ));

        $schedule->assignments()->delete();
        foreach ($request->personnel_ids as $personnelId) {
            $schedule->assignments()->create(['personnel_id' => $personnelId]);
        }

        // Remove only pending tasks (preserve completed/missed) then regenerate
        $schedule->collectionTasks()->where('status', 'pending')->delete();
        $this->generateCollectionTasks($schedule, $request->personnel_ids);

        return back()->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return back()->with('success', 'Schedule deleted successfully.');
    }

    public function tasks(Schedule $schedule)
    {
        $tasks = CollectionTask::with('personnel')
            ->where('schedule_id', $schedule->id)
            ->whereDate('collection_date', '>=', today())
            ->orderBy('collection_date')
            ->get();

        return response()->json($tasks);
    }

    public function reassignTask(Request $request, CollectionTask $task)
    {
        $request->validate([
            'personnel_id' => ['required', 'exists:users,id'],
        ]);

        $task->update([
            'personnel_id' => $request->personnel_id,
        ]);

        return back()->with('success', 'Task reassigned successfully.');
    }

    /**
     * Generate CollectionTask records for each personnel on each collection date.
     */
    private function generateCollectionTasks(Schedule $schedule, array $personnelIds): void
    {
        $dates = $this->getCollectionDates($schedule);

        foreach ($dates as $date) {
            foreach ($personnelIds as $personnelId) {
                // Avoid duplicate tasks
                $exists = CollectionTask::where('schedule_id', $schedule->id)
                    ->where('personnel_id', $personnelId)
                    ->where('collection_date', $date->toDateString())
                    ->exists();

                if (! $exists) {
                    CollectionTask::create([
                        'schedule_id' => $schedule->id,
                        'personnel_id' => $personnelId,
                        'collection_date' => $date->toDateString(),
                        'status' => 'pending',
                    ]);
                }
            }
        }
    }

    /**
     * Calculate collection dates based on frequency.
     * Caps at 90 days into the future to avoid generating thousands of rows.
     */
    private function getCollectionDates(Schedule $schedule): array
    {
        $start = Carbon::parse($schedule->start_date)->startOfDay();
        $end = $schedule->end_date
            ? Carbon::parse($schedule->end_date)->startOfDay()
            : $start->copy()->addDays(90); // default: 90 days max

        // Safety cap: never generate more than 90 days out
        $maxEnd = now()->addDays(90)->startOfDay();
        if ($end->greaterThan($maxEnd)) {
            $end = $maxEnd;
        }

        // Don't generate tasks in the past
        if ($start->lessThan(today())) {
            $start = today();
        }

        $dates = [];

        switch ($schedule->frequency) {
            case 'once':
                $dates[] = Carbon::parse($schedule->start_date);
                break;

            case 'daily':
                $current = $start->copy();
                while ($current->lessThanOrEqualTo($end)) {
                    $dates[] = $current->copy();
                    $current->addDay();
                }
                break;

            case 'weekly':
                $current = $start->copy();
                while ($current->lessThanOrEqualTo($end)) {
                    $dates[] = $current->copy();
                    $current->addWeek();
                }
                break;

            case 'monthly':
                $current = $start->copy();
                while ($current->lessThanOrEqualTo($end)) {
                    $dates[] = $current->copy();
                    $current->addMonth();
                }
                break;
        }

        return $dates;
    }
}
