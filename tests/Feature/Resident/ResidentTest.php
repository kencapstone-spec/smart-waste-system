<?php

namespace Tests\Feature\Resident;

use App\Models\CollectionTask;
use App\Models\Point;
use App\Models\Report;
use App\Models\Schedule;
use App\Models\Street;
use App\Models\User;
use App\Models\Zone; // <--- Added this import
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResidentTest extends TestCase
{
    use RefreshDatabase;

    private function resident(?Street $street = null): User
    {
        return User::factory()->resident()->create([
            'street_id' => $street?->id,
        ]);
    }

    private function makeStreet(): Street
    {
        $zone = Zone::create(['name' => 'Zone 1']);

        return Street::create(['zone_id' => $zone->id, 'name' => 'Rizal St.']);
    }

    // ---------------------------------------------------------------
    // Access Control
    // ---------------------------------------------------------------

    public function test_guest_cannot_access_resident_area(): void
    {
        $this->get('/resident/schedules')->assertRedirect('/login');
    }

    public function test_official_cannot_access_resident_area(): void
    {
        $user = User::factory()->official()->create();
        $this->actingAs($user)->get('/resident/schedules')->assertForbidden();
    }

    public function test_personnel_cannot_access_resident_area(): void
    {
        $user = User::factory()->personnel()->create();
        $this->actingAs($user)->get('/resident/schedules')->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Schedules
    // ---------------------------------------------------------------

    public function test_resident_can_view_schedules_page(): void
    {
        $response = $this->actingAs($this->resident())->get('/resident/schedules');
        $response->assertStatus(200);
        $response->assertInertia(fn ($p) => $p->component('Resident/Schedules/Index'));
    }

    public function test_resident_sees_only_their_street_schedules(): void
    {
        $street = $this->makeStreet();
        $resident = $this->resident($street);
        $official = User::factory()->official()->create();

        $mySchedule = Schedule::create([
            'street_id' => $street->id,
            'created_by' => $official->id,
            'title' => 'My Street Schedule',
            'frequency' => 'weekly',
            'start_date' => now()->toDateString(),
            'collection_time' => '07:00',
            'status' => 'active',
        ]);

        $otherZone = Zone::create(['name' => 'Zone 2']);
        $otherStreet = Street::create(['zone_id' => $otherZone->id, 'name' => 'Other St.']);
        Schedule::create([
            'street_id' => $otherStreet->id,
            'created_by' => $official->id,
            'title' => 'Other Street Schedule',
            'frequency' => 'weekly',
            'start_date' => now()->toDateString(),
            'collection_time' => '07:00',
            'status' => 'active',
        ]);

        $response = $this->actingAs($resident)->get('/resident/schedules');
        $response->assertInertia(fn ($p) => $p->component('Resident/Schedules/Index')
            ->where('schedules.0.title', 'My Street Schedule')
            ->count('schedules', 1)
        );
    }

    // ---------------------------------------------------------------
    // Reports
    // ---------------------------------------------------------------

    public function test_resident_can_view_reports_page(): void
    {
        $response = $this->actingAs($this->resident())->get('/resident/reports');
        $response->assertStatus(200);
        $response->assertInertia(fn ($p) => $p->component('Resident/Reports/Index'));
    }

    public function test_resident_can_submit_missed_collection_report(): void
    {
        $resident = $this->resident();

        $response = $this->actingAs($resident)->post('/resident/reports', [
            'type' => 'missed_collection',
            'description' => 'Garbage was not collected this Monday.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reports', [
            'resident_id' => $resident->id,
            'type' => 'missed_collection',
            'status' => 'pending',
        ]);
    }

    public function test_resident_can_submit_illegal_dumping_report(): void
    {
        $resident = $this->resident();

        $response = $this->actingAs($resident)->post('/resident/reports', [
            'type' => 'illegal_dumping',
            'description' => 'Someone is dumping trash near the river.',
            'latitude' => 10.1236,
            'longitude' => 124.0030,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reports', [
            'resident_id' => $resident->id,
            'type' => 'illegal_dumping',
        ]);
    }

    public function test_resident_can_submit_report_with_photo(): void
    {
        Storage::fake('public');
        $resident = $this->resident();

        $response = $this->actingAs($resident)->post('/resident/reports', [
            'type' => 'missed_collection',
            'description' => 'Garbage not collected.',
            'photos' => [UploadedFile::fake()->image('evidence.jpg')],
        ]);

        $response->assertRedirect();
        $report = Report::where('resident_id', $resident->id)->first();
        $this->assertNotNull($report);
        $this->assertDatabaseHas('report_photos', ['report_id' => $report->id]);
    }

    public function test_report_submission_requires_type(): void
    {
        $response = $this->actingAs($this->resident())->post('/resident/reports', [
            'description' => 'No type provided.',
        ]);
        $response->assertSessionHasErrors('type');
    }

    public function test_report_submission_requires_description(): void
    {
        $response = $this->actingAs($this->resident())->post('/resident/reports', [
            'type' => 'missed_collection',
        ]);
        $response->assertSessionHasErrors('description');
    }

    public function test_resident_only_sees_own_reports(): void
    {
        $resident = $this->resident();
        $other = $this->resident();

        Report::create([
            'resident_id' => $resident->id,
            'type' => 'missed_collection',
            'description' => 'My report',
            'status' => 'pending',
        ]);
        Report::create([
            'resident_id' => $other->id,
            'type' => 'illegal_dumping',
            'description' => 'Other report',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($resident)->get('/resident/reports');
        $response->assertInertia(fn ($p) => $p->component('Resident/Reports/Index')
            ->count('reports.data', 1)
        );
    }

    // ---------------------------------------------------------------
    // Points
    // ---------------------------------------------------------------

    public function test_resident_can_view_points_page(): void
    {
        $response = $this->actingAs($this->resident())->get('/resident/points');
        $response->assertStatus(200);
        $response->assertInertia(fn ($p) => $p->component('Resident/Points/Index'));
    }

    public function test_resident_sees_correct_total_points(): void
    {
        $resident = $this->resident();
        $personnel = User::factory()->personnel()->create();

        // --- Fix: Create schedule and task ---
        $official = User::factory()->official()->create();
        $street = $this->makeStreet();

        $schedule = Schedule::create([
            'street_id' => $street->id,
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
        // -------------------------------------

        Point::create(['resident_id' => $resident->id, 'awarded_by' => $personnel->id, 'collection_task_id' => $task->id, 'points' => 10]);
        Point::create(['resident_id' => $resident->id, 'awarded_by' => $personnel->id, 'collection_task_id' => $task->id, 'points' => 20]);

        $response = $this->actingAs($resident)->get('/resident/points');
        $response->assertInertia(fn ($p) => $p->component('Resident/Points/Index')
            ->where('totalPoints', 30)
        );
    }

    public function test_resident_only_sees_own_points(): void
    {
        $resident = $this->resident();
        $other = $this->resident();
        $personnel = User::factory()->personnel()->create();

        // --- Fix: Create schedule and task ---
        $official = User::factory()->official()->create();
        $street = $this->makeStreet();

        $schedule = Schedule::create([
            'street_id' => $street->id,
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
        // -------------------------------------

        Point::create(['resident_id' => $resident->id, 'awarded_by' => $personnel->id, 'collection_task_id' => $task->id, 'points' => 5]);
        Point::create(['resident_id' => $other->id,    'awarded_by' => $personnel->id, 'collection_task_id' => $task->id, 'points' => 50]);

        $response = $this->actingAs($resident)->get('/resident/points');
        $response->assertInertia(fn ($p) => $p->component('Resident/Points/Index')
            ->where('totalPoints', 5)
        );
    }
}
