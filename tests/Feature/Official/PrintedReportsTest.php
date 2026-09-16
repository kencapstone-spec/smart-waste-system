<?php

namespace Tests\Feature\Official;

use App\Models\CollectionTask;
use App\Models\Point;
use App\Models\Report;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintedReportsTest extends TestCase
{
    use RefreshDatabase;

    private function official(): User
    {
        return User::factory()->official()->create();
    }

    private function resident(): User
    {
        return User::factory()->resident()->create();
    }

    private function makeZone(string $name = 'Purok Centro'): Zone
    {
        return Zone::firstOrCreate(['name' => $name]);
    }

    // ---------------------------------------------------------------
    // Access Control
    // ---------------------------------------------------------------

    public function test_guest_cannot_access_printed_reports(): void
    {
        $this->get('/official/printed-reports')->assertRedirect('/login');
        $this->get('/official/pdf/collection-summary')->assertRedirect('/login');
    }

    public function test_resident_cannot_access_printed_reports(): void
    {
        $resident = $this->resident();

        $this->actingAs($resident)->get('/official/printed-reports')->assertForbidden();
        $this->actingAs($resident)->get('/official/pdf/collection-summary')->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Printed Reports Hub
    // ---------------------------------------------------------------

    public function test_official_can_view_printed_reports_center(): void
    {
        $official = $this->official();
        $zone = $this->makeZone();

        $response = $this->actingAs($official)->get('/official/printed-reports');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Official/PrintedReports/Index')
            ->has('zones')
            ->has('stats')
            ->has('recentReports')
        );
    }

    // ---------------------------------------------------------------
    // Collection Activity Summary PDF
    // ---------------------------------------------------------------

    public function test_official_can_generate_collection_summary_pdf_stream(): void
    {
        $official = $this->official();
        $zone = $this->makeZone();
        $personnel = User::factory()->personnel()->create();

        $schedule = Schedule::create([
            'zone_id' => $zone->id,
            'created_by' => $official->id,
            'title' => 'Morning Route',
            'frequency' => 'weekly',
            'start_date' => '2026-07-01',
            'collection_time' => '07:00:00',
            'status' => 'active',
        ]);

        CollectionTask::create([
            'schedule_id' => $schedule->id,
            'personnel_id' => $personnel->id,
            'collection_date' => '2026-07-01',
            'status' => 'completed',
            'remarks' => 'Collected on time',
        ]);

        $response = $this->actingAs($official)->get('/official/pdf/collection-summary?action=stream');

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_official_can_download_collection_summary_pdf_with_filters(): void
    {
        $official = $this->official();
        $zone = $this->makeZone();

        $response = $this->actingAs($official)->get('/official/pdf/collection-summary?action=download&zone_id=' . $zone->id . '&status=completed&from=2026-01-01&to=2026-12-31');

        $response->assertOk();
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('collection-activity-summary.pdf', $response->headers->get('content-disposition'));
    }

    // ---------------------------------------------------------------
    // Complaints Summary PDF
    // ---------------------------------------------------------------

    public function test_official_can_generate_complaints_summary_pdf(): void
    {
        $official = $this->official();
        $resident = $this->resident();

        Report::create([
            'resident_id' => $resident->id,
            'type' => 'missed_collection',
            'description' => 'Truck skipped house #14',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($official)->get('/official/pdf/complaints-summary?action=stream&type=missed_collection&status=pending');

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    // ---------------------------------------------------------------
    // Resident Participation & Points PDF
    // ---------------------------------------------------------------

    public function test_official_can_generate_resident_participation_pdf(): void
    {
        $official = $this->official();
        $resident = $this->resident();
        $resident->update(['status' => 'active']);
        $zone = $this->makeZone();
        $personnel = User::factory()->personnel()->create();

        $schedule = Schedule::create([
            'zone_id' => $zone->id,
            'created_by' => $official->id,
            'title' => 'Route A',
            'frequency' => 'weekly',
            'start_date' => '2026-07-01',
            'collection_time' => '07:00:00',
            'status' => 'active',
        ]);

        $task = CollectionTask::create([
            'schedule_id' => $schedule->id,
            'personnel_id' => $personnel->id,
            'collection_date' => '2026-07-01',
            'status' => 'completed',
        ]);

        Point::create([
            'resident_id' => $resident->id,
            'awarded_by' => $official->id,
            'collection_task_id' => $task->id,
            'points' => 25,
            'remarks' => 'Proper waste segregation',
        ]);

        $response = $this->actingAs($official)->get('/official/pdf/resident-participation?action=stream&min_points=10&sort=points');

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    // ---------------------------------------------------------------
    // Single Complaint Incident Sheet PDF
    // ---------------------------------------------------------------

    public function test_official_can_generate_single_complaint_incident_pdf(): void
    {
        $official = $this->official();
        $resident = $this->resident();

        $report = Report::create([
            'resident_id' => $resident->id,
            'type' => 'illegal_dumping',
            'description' => 'Discarded plastics near creek',
            'status' => 'resolved',
            'official_response' => 'Clean-up crew dispatched and area cleared.',
            'responded_by' => $official->id,
            'responded_at' => now(),
            'latitude' => 10.1500,
            'longitude' => 124.3300,
        ]);

        $response = $this->actingAs($official)->get("/official/pdf/complaints/{$report->id}?action=stream");

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    // ---------------------------------------------------------------
    // Schedules Summary Master List PDF
    // ---------------------------------------------------------------

    public function test_official_can_generate_schedules_summary_pdf(): void
    {
        $official = $this->official();
        $zone = $this->makeZone();

        Schedule::create([
            'zone_id' => $zone->id,
            'created_by' => $official->id,
            'title' => 'Purok Centro Weekly Route',
            'frequency' => 'weekly',
            'start_date' => '2026-07-01',
            'collection_time' => '08:00:00',
            'status' => 'active',
        ]);

        $response = $this->actingAs($official)->get('/official/pdf/schedules?action=stream&status=active');

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
