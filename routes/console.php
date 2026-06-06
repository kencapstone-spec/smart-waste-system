<?php

use App\Models\CollectionTask;
use App\Models\Schedule as WasteSchedule;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring; // Added to read the assignments table
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tasks:generate', function () {
    $today = Carbon::today();

    // 1. Find all active schedules valid for today
    $schedules = WasteSchedule::where('status', 'active')
        ->whereDate('start_date', '<=', $today)
        ->where(function ($query) use ($today) {
            $query->whereNull('end_date')
                ->orWhereDate('end_date', '>=', $today);
        })->get();

    $count = 0;

    foreach ($schedules as $schedule) {
        $shouldGenerate = false;
        $start = Carbon::parse($schedule->start_date)->startOfDay();

        // Respect the schedule's frequency
        switch ($schedule->frequency) {
            case 'once':
                $shouldGenerate = $start->isSameDay($today);
                break;
            case 'daily':
                $shouldGenerate = true;
                break;
            case 'weekly':
                $shouldGenerate = $start->dayOfWeek === $today->dayOfWeek;
                break;
            case 'monthly':
                $shouldGenerate = $start->day === $today->day;
                break;
        }

        if (! $shouldGenerate) {
            continue;
        }

        // 2. Find ALL personnel assigned to this specific schedule
        $assignments = DB::table('schedule_assignments')
            ->where('schedule_id', $schedule->id)
            ->get();

        // 3. Create a daily task for EACH assigned personnel
        foreach ($assignments as $assignment) {

            // Check if this specific worker already has this task today
            $taskExists = CollectionTask::where('schedule_id', $schedule->id)
                ->where('personnel_id', $assignment->personnel_id)
                ->whereDate('collection_date', $today)
                ->exists();

            if (! $taskExists) {
                CollectionTask::create([
                    'schedule_id' => $schedule->id,
                    'personnel_id' => $assignment->personnel_id,
                    'collection_date' => $today,
                    'status' => 'pending',
                ]);
                $count++;
            }
        }
    }

    $this->info("Success! Generated {$count} tasks for today.");
})->purpose('Generate daily collection tasks from active schedules');

Artisan::command('tasks:cleanup', function () {
    $today = Carbon::today();
    $count = CollectionTask::where('status', 'pending')
        ->whereDate('collection_date', '<', $today)
        ->update(['status' => 'missed']);

    $this->info("Success! Marked {$count} pending tasks from past dates as missed.");
})->purpose('Mark past pending tasks as missed');

// Run automatically at midnight
Schedule::command('tasks:generate')->dailyAt('00:00');
Schedule::command('tasks:cleanup')->dailyAt('00:05');
