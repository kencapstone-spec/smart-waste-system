<?php

namespace Tests\Unit;

use App\Models\CollectionTask;
use App\Models\Point;
use App\Models\Report;
use App\Models\Schedule;
use App\Models\ScheduleAssignment;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    private function makeZone(): Zone
    {
        return Zone::create(['name' => 'Zone 1']);
    }

    // ---------------------------------------------------------------
    // User Model
    // ---------------------------------------------------------------

    public function test_user_is_super_admin(): void
    {
        $user = User::factory()->superAdmin()->create();
        $this->assertTrue($user->isSuperAdmin());
        $this->assertFalse($user->isAdmin());
    }

    public function test_user_is_barangay_official(): void
    {
        $user = User::factory()->official()->create();
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isResident());
    }

    public function test_user_is_personnel(): void
    {
        $user = User::factory()->personnel()->create();
        $this->assertTrue($user->isPersonnel());
        $this->assertFalse($user->isAdmin());
    }

    public function test_user_is_resident(): void
    {
        $user = User::factory()->resident()->create();
        $this->assertTrue($user->isResident());
        $this->assertFalse($user->isPersonnel());
    }

    public function test_user_belongs_to_zone(): void
    {
        $zone = $this->makeZone();
        $user = User::factory()->resident()->create(['zone_id' => $zone->id]);

        $this->assertEquals($zone->id, $user->zone->id);
    }

    public function test_user_has_many_reports(): void
    {
        $resident = User::factory()->resident()->create();
        Report::create([
            'resident_id' => $resident->id,
            'type' => 'missed_collection',
            'description' => 'Test',
            'status' => 'pending',
        ]);

        $this->assertCount(1, $resident->reports);
    }

    public function test_user_has_many_points(): void
    {
        $resident = User::factory()->resident()->create();
        $personnel = User::factory()->personnel()->create();

        $official = User::factory()->official()->create();
        $zone = $this->makeZone();

        $schedule = Schedule::create([
            'zone_id' => $zone->id,
            'created_by' => $official->id,
            'title' => 'Test Schedule',
            'frequency' => 'once',
            'start_date' => now()->toDateString(),
            'collection_time' => '07:00',
            'status' => 'active',
        ]);

        $task = CollectionTask::create([
            'schedule_id' => $schedule->id,
            'personnel_id' => $personnel->id,
            'collection_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        Point::create(['resident_id' => $resident->id, 'awarded_by' => $personnel->id, 'collection_task_id' => $task->id, 'points' => 10]);
        Point::create(['resident_id' => $resident->id, 'awarded_by' => $personnel->id, 'collection_task_id' => $task->id, 'points' => 20]);

        $this->assertCount(2, $resident->points);
        $this->assertEquals(30, $resident->points->sum('points'));
    }

    // ---------------------------------------------------------------
    // Schedule Model
    // ---------------------------------------------------------------

    public function test_schedule_belongs_to_zone(): void
    {
        $zone = $this->makeZone();
        $official = User::factory()->official()->create();

        $schedule = Schedule::create([
            'zone_id' => $zone->id,
            'created_by' => $official->id,
            'title' => 'Test',
            'frequency' => 'weekly',
            'start_date' => now()->toDateString(),
            'collection_time' => '07:00',
            'status' => 'active',
        ]);

        $this->assertEquals($zone->id, $schedule->zone->id);
    }

    public function test_schedule_has_many_assignments(): void
    {
        $zone = $this->makeZone();
        $official = User::factory()->official()->create();
        $personnel = User::factory()->personnel()->create();

        $schedule = Schedule::create([
            'zone_id' => $zone->id,
            'created_by' => $official->id,
            'title' => 'Test',
            'frequency' => 'weekly',
            'start_date' => now()->toDateString(),
            'collection_time' => '07:00',
            'status' => 'active',
        ]);

        ScheduleAssignment::create(['schedule_id' => $schedule->id, 'personnel_id' => $personnel->id]);

        $this->assertCount(1, $schedule->assignments);
    }

    // ---------------------------------------------------------------
    // CollectionTask Model
    // ---------------------------------------------------------------

    public function test_collection_task_belongs_to_schedule_and_personnel(): void
    {
        $zone = $this->makeZone();
        $official = User::factory()->official()->create();
        $personnel = User::factory()->personnel()->create();

        $schedule = Schedule::create([
            'zone_id' => $zone->id,
            'created_by' => $official->id,
            'title' => 'Test',
            'frequency' => 'once',
            'start_date' => now()->toDateString(),
            'collection_time' => '07:00',
            'status' => 'active',
        ]);

        $task = CollectionTask::create([
            'schedule_id' => $schedule->id,
            'personnel_id' => $personnel->id,
            'collection_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->assertEquals($schedule->id, $task->schedule->id);
        $this->assertEquals($personnel->id, $task->personnel->id);
    }

    // ---------------------------------------------------------------
    // Report Model
    // ---------------------------------------------------------------

    public function test_report_belongs_to_resident(): void
    {
        $resident = User::factory()->resident()->create();
        $report = Report::create([
            'resident_id' => $resident->id,
            'type' => 'illegal_dumping',
            'description' => 'Near the river.',
            'status' => 'pending',
        ]);

        $this->assertEquals($resident->id, $report->resident->id);
    }
}
