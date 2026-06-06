<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->superAdmin()->create();
    }

    // ---------------------------------------------------------------
    // Access Control
    // ---------------------------------------------------------------

    public function test_guest_cannot_access_super_admin_area(): void
    {
        $this->get('/super-admin/users')->assertRedirect('/login');
    }

    public function test_official_cannot_access_super_admin_area(): void
    {
        $user = User::factory()->official()->create();
        $this->actingAs($user)->get('/super-admin/users')->assertForbidden();
    }

    public function test_resident_cannot_access_super_admin_area(): void
    {
        $user = User::factory()->resident()->create();
        $this->actingAs($user)->get('/super-admin/users')->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Users
    // ---------------------------------------------------------------

    public function test_super_admin_can_view_users_page(): void
    {
        $response = $this->actingAs($this->superAdmin())->get('/super-admin/users');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('SuperAdmin/Users/Index'));
    }

    public function test_super_admin_can_create_official(): void
    {
        $response = $this->actingAs($this->superAdmin())->post('/super-admin/users', [
            'name' => 'Maria Santos',
            'phone' => '09181234567',
            'role' => 'barangay_official',
        ]);

        $response->assertRedirect('/super-admin/users');
        $this->assertDatabaseHas('users', [
            'phone' => '09181234567',
            'role' => 'barangay_official',
            'status' => 'active',
        ]);
    }

    public function test_super_admin_can_create_personnel(): void
    {
        $response = $this->actingAs($this->superAdmin())->post('/super-admin/users', [
            'name' => 'Pedro Reyes',
            'phone' => '09191234567',
            'role' => 'personnel',
        ]);

        $response->assertRedirect('/super-admin/users');
        $this->assertDatabaseHas('users', ['role' => 'personnel']);
    }

    public function test_create_user_fails_with_invalid_phone(): void
    {
        $response = $this->actingAs($this->superAdmin())->post('/super-admin/users', [
            'name' => 'Test User',
            'phone' => '1234',
            'role' => 'personnel',
        ]);
        $response->assertSessionHasErrors('phone');
    }

    public function test_create_user_fails_with_invalid_role(): void
    {
        $response = $this->actingAs($this->superAdmin())->post('/super-admin/users', [
            'name' => 'Test User',
            'phone' => '09171234567',
            'role' => 'resident',  // not allowed via super admin
        ]);
        $response->assertSessionHasErrors('role');
    }

    public function test_super_admin_can_update_user(): void
    {
        $user = User::factory()->personnel()->create();

        $response = $this->actingAs($this->superAdmin())->put("/super-admin/users/{$user->id}", [
            'name' => 'Updated Name',
            'phone' => $user->phone,
            'role' => 'barangay_official',
            'status' => 'active',
        ]);

        $response->assertRedirect('/super-admin/users');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'barangay_official']);
    }

    public function test_super_admin_can_delete_user(): void
    {
        $user = User::factory()->personnel()->create();

        $response = $this->actingAs($this->superAdmin())->delete("/super-admin/users/{$user->id}");
        $response->assertRedirect('/super-admin/users');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    // ---------------------------------------------------------------
    // Zones
    // ---------------------------------------------------------------

    public function test_super_admin_can_view_zones(): void
    {
        $response = $this->actingAs($this->superAdmin())->get('/super-admin/zones');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('SuperAdmin/Zones/Index'));
    }

    public function test_super_admin_can_create_zone(): void
    {
        $response = $this->actingAs($this->superAdmin())->post('/super-admin/zones', [
            'name' => 'Zone 1',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('zones', ['name' => 'Zone 1']);
    }

    public function test_super_admin_can_delete_zone(): void
    {
        $zone = Zone::create(['name' => 'Zone to Delete']);
        $response = $this->actingAs($this->superAdmin())->delete("/super-admin/zones/{$zone->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('zones', ['id' => $zone->id]);
    }

    // ---------------------------------------------------------------
    // Streets
    // ---------------------------------------------------------------

    public function test_super_admin_can_view_streets(): void
    {
        $response = $this->actingAs($this->superAdmin())->get('/super-admin/streets');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('SuperAdmin/Streets/Index'));
    }

    public function test_super_admin_can_create_street(): void
    {
        $zone = Zone::create(['name' => 'Zone 1']);
        $response = $this->actingAs($this->superAdmin())->post('/super-admin/streets', [
            'name' => 'Rizal St.',
            'zone_id' => $zone->id,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('streets', ['name' => 'Rizal St.']);
    }

    // ---------------------------------------------------------------
    // System Logs
    // ---------------------------------------------------------------

    public function test_super_admin_can_view_system_logs(): void
    {
        $response = $this->actingAs($this->superAdmin())->get('/super-admin/system-logs');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('SuperAdmin/SystemLogs/Index'));
    }
}
