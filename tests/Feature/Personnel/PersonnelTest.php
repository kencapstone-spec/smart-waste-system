<?php

namespace Tests\Feature\Personnel;

use App\Models\CollectionTask;
use App\Models\Schedule;
use App\Models\ScheduleAssignment;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PersonnelTest extends TestCase
{
    use RefreshDatabase;

    private function personnel(): User
    {
        return User::factory()->personnel()->create();
    }

    private function makeScheduleWithTask(User $personnel): CollectionTask
    {
        $zone = Zone::create(['name' => 'Zone 1']);
        $official = User::factory()->official()->create();

        $schedule = Schedule::create([
            'zone_id' => $zone->id,
            'created_by' => $official->id,
            'title' => 'Test Schedule',
            'frequency' => 'once',
            'start_date' => now()->toDateString(),
            'collection_time' => '07:00',
            'status' => 'active',
        ]);

        ScheduleAssignment::create([
            'schedule_id' => $schedule->id,
            'personnel_id' => $personnel->id,
        ]);

        return CollectionTask::create([
            'schedule_id' => $schedule->id,
            'personnel_id' => $personnel->id,
            'collection_date' => now()->toDateString(),
            'status' => 'pending',
        ]);
    }

    // ---------------------------------------------------------------
    // Access Control
    // ---------------------------------------------------------------

    public function test_guest_cannot_access_personnel_area(): void
    {
        $this->get('/personnel/schedules')->assertRedirect('/login');
    }

    public function test_resident_cannot_access_personnel_area(): void
    {
        $user = User::factory()->resident()->create();
        $this->actingAs($user)->get('/personnel/schedules')->assertForbidden();
    }

    public function test_official_cannot_access_personnel_area(): void
    {
        $user = User::factory()->official()->create();
        $this->actingAs($user)->get('/personnel/schedules')->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Schedules
    // ---------------------------------------------------------------

    public function test_personnel_can_view_assigned_schedules(): void
    {
        $response = $this->actingAs($this->personnel())->get('/personnel/schedules');
        $response->assertStatus(200);
        $response->assertInertia(fn ($p) => $p->component('Personnel/Schedules/Index'));
    }

    // ---------------------------------------------------------------
    // Collection Tasks
    // ---------------------------------------------------------------

    public function test_personnel_can_view_tasks(): void
    {
        $response = $this->actingAs($this->personnel())->get('/personnel/tasks');
        $response->assertStatus(200);
        $response->assertInertia(fn ($p) => $p->component('Personnel/CollectionTasks/Index'));
    }

    public function test_personnel_can_update_task_status(): void
    {
        $personnel = $this->personnel();
        $task = $this->makeScheduleWithTask($personnel);

        $response = $this->actingAs($personnel)->post("/personnel/tasks/{$task->id}/update-status", [
            'status' => 'completed',
            'remarks' => 'All collected.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('collection_tasks', [
            'id' => $task->id,
            'status' => 'completed',
            'remarks' => 'All collected.',
        ]);
    }

    public function test_personnel_can_upload_photo_proof(): void
    {
        Storage::fake('public');

        $personnel = $this->personnel();
        $task = $this->makeScheduleWithTask($personnel);

        $response = $this->actingAs($personnel)->post("/personnel/tasks/{$task->id}/update-status", [
            'status' => 'completed',
            'photos' => [UploadedFile::fake()->image('proof.jpg')],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('collection_task_photos', ['collection_task_id' => $task->id]);
    }

    public function test_personnel_cannot_update_other_personnel_task(): void
    {
        $personnel = $this->personnel();
        $other = $this->personnel();
        $task = $this->makeScheduleWithTask($other); // belongs to $other

        $response = $this->actingAs($personnel)->post("/personnel/tasks/{$task->id}/update-status", [
            'status' => 'completed',
        ]);

        $response->assertForbidden();
    }

    public function test_personnel_update_task_requires_valid_status(): void
    {
        $personnel = $this->personnel();
        $task = $this->makeScheduleWithTask($personnel);

        $response = $this->actingAs($personnel)->post("/personnel/tasks/{$task->id}/update-status", [
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors('status');
    }

    // ---------------------------------------------------------------
    // Award Points
    // ---------------------------------------------------------------

    public function test_personnel_can_award_points_to_resident(): void
    {
        $personnel = $this->personnel();
        $task = $this->makeScheduleWithTask($personnel);
        $resident = User::factory()->resident()->create();

        $response = $this->actingAs($personnel)->post("/personnel/tasks/{$task->id}/award-points", [
            'resident_id' => $resident->id,
            'points' => 15,
            'remarks' => 'Good segregation',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('points', [
            'resident_id' => $resident->id,
            'awarded_by' => $personnel->id,
            'collection_task_id' => $task->id,
            'points' => 15,
        ]);
    }

    public function test_award_points_fails_with_zero_points(): void
    {
        $personnel = $this->personnel();
        $task = $this->makeScheduleWithTask($personnel);
        $resident = User::factory()->resident()->create();

        $response = $this->actingAs($personnel)->post("/personnel/tasks/{$task->id}/award-points", [
            'resident_id' => $resident->id,
            'points' => 0,
        ]);

        $response->assertSessionHasErrors('points');
    }

    public function test_award_points_fails_with_nonexistent_resident(): void
    {
        $personnel = $this->personnel();
        $task = $this->makeScheduleWithTask($personnel);

        $response = $this->actingAs($personnel)->post("/personnel/tasks/{$task->id}/award-points", [
            'resident_id' => 99999,
            'points' => 10,
        ]);

        $response->assertSessionHasErrors('resident_id');
    }
}
