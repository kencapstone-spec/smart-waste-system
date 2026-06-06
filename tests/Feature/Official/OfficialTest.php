<?php

namespace Tests\Feature\Official;

use App\Models\Report;
use App\Models\Schedule;
use App\Models\Street;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficialTest extends TestCase
{
    use RefreshDatabase;

    private function official(): User
    {
        return User::factory()->official()->create();
    }

    private function makeStreet(): Street
    {
        $zone = Zone::create(['name' => 'Zone 1']);

        return Street::create(['zone_id' => $zone->id, 'name' => 'Rizal St.']);
    }

    // ---------------------------------------------------------------
    // Access Control
    // ---------------------------------------------------------------

    public function test_guest_cannot_access_official_area(): void
    {
        $this->get('/official/schedules')->assertRedirect('/login');
    }

    public function test_resident_cannot_access_official_area(): void
    {
        $user = User::factory()->resident()->create();
        $this->actingAs($user)->get('/official/schedules')->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Schedules
    // ---------------------------------------------------------------

    public function test_official_can_view_schedules(): void
    {
        $response = $this->actingAs($this->official())->get('/official/schedules');
        $response->assertStatus(200);
        $response->assertInertia(fn ($p) => $p->component('Official/Schedules/Index'));
    }

    public function test_official_can_create_schedule(): void
    {
        $official = $this->official();
        $street = $this->makeStreet();
        $personnel = User::factory()->personnel()->create();

        $response = $this->actingAs($official)->post('/official/schedules', [
            'street_id' => $street->id,
            'title' => 'Weekly Collection',
            'description' => 'Every Monday',
            'frequency' => 'weekly',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'collection_time' => '07:00',
            'personnel_ids' => [$personnel->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('schedules', ['title' => 'Weekly Collection']);
        $this->assertDatabaseHas('schedule_assignments', ['personnel_id' => $personnel->id]);
    }

    public function test_official_cannot_create_schedule_without_personnel(): void
    {
        $official = $this->official();
        $street = $this->makeStreet();

        $response = $this->actingAs($official)->post('/official/schedules', [
            'street_id' => $street->id,
            'title' => 'No Personnel',
            'frequency' => 'weekly',
            'start_date' => '2026-07-01',
            'collection_time' => '07:00',
            'personnel_ids' => [],
        ]);

        $response->assertSessionHasErrors('personnel_ids');
    }

    public function test_official_can_delete_schedule(): void
    {
        $official = $this->official();
        $street = $this->makeStreet();
        $schedule = Schedule::create([
            'street_id' => $street->id,
            'created_by' => $official->id,
            'title' => 'To Delete',
            'frequency' => 'once',
            'start_date' => '2026-07-01',
            'collection_time' => '07:00',
            'status' => 'active',
        ]);

        $response = $this->actingAs($official)->delete("/official/schedules/{$schedule->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }

    // ---------------------------------------------------------------
    // Reports
    // ---------------------------------------------------------------

    public function test_official_can_view_reports(): void
    {
        $response = $this->actingAs($this->official())->get('/official/reports');
        $response->assertStatus(200);
        $response->assertInertia(fn ($p) => $p->component('Official/Reports/Index'));
    }

    public function test_official_can_respond_to_report(): void
    {
        $official = $this->official();
        $resident = User::factory()->resident()->create();
        $report = Report::create([
            'resident_id' => $resident->id,
            'type' => 'missed_collection',
            'description' => 'Garbage not collected.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($official)->post("/official/reports/{$report->id}/respond", [
            'official_response' => 'We will send personnel.',
            'status' => 'resolved',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'resolved',
            'official_response' => 'We will send personnel.',
        ]);
    }

    public function test_official_respond_requires_response_text(): void
    {
        $official = $this->official();
        $resident = User::factory()->resident()->create();
        $report = Report::create([
            'resident_id' => $resident->id,
            'type' => 'illegal_dumping',
            'description' => 'Dumping near river.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($official)->post("/official/reports/{$report->id}/respond", [
            'official_response' => '',
            'status' => 'reviewed',
        ]);

        $response->assertSessionHasErrors('official_response');
    }

    // ---------------------------------------------------------------
    // Residents
    // ---------------------------------------------------------------

    public function test_official_can_view_residents(): void
    {
        $response = $this->actingAs($this->official())->get('/official/residents');
        $response->assertStatus(200);
        $response->assertInertia(fn ($p) => $p->component('Official/Residents/Index'));
    }

    public function test_official_can_approve_resident(): void
    {
        $official = $this->official();
        $resident = User::factory()->pending()->resident()->create();

        $response = $this->actingAs($official)->post("/official/residents/{$resident->id}/approve");
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $resident->id, 'status' => 'active']);
    }

    public function test_official_can_reject_resident(): void
    {
        $official = $this->official();
        $resident = User::factory()->pending()->resident()->create();

        $response = $this->actingAs($official)->post("/official/residents/{$resident->id}/reject");
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $resident->id, 'status' => 'rejected']);
    }

    public function test_official_can_view_resident_detail(): void
    {
        $official = $this->official();
        $resident = User::factory()->resident()->create();

        $response = $this->actingAs($official)->get("/official/residents/{$resident->id}");
        $response->assertStatus(200);
        $response->assertInertia(fn ($p) => $p->component('Official/Residents/Show'));
    }
}
