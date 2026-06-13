<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // Login Page
    // ---------------------------------------------------------------

    public function test_login_page_is_accessible_to_guests(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Auth/AuthScreen'));
    }

    public function test_authenticated_user_is_redirected_from_login(): void
    {
        $user = User::factory()->official()->create();
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/dashboard');
    }

    // ---------------------------------------------------------------
    // Send OTP
    // ---------------------------------------------------------------

    public function test_send_otp_fails_for_nonexistent_phone(): void
    {
        $response = $this->post('/login/send-otp', ['phone' => '09000000000']);
        $response->assertSessionHasErrors('phone');
    }

    public function test_send_otp_fails_for_pending_resident(): void
    {
        $user = User::factory()->pending()->create(['role' => 'resident']);
        $response = $this->post('/login/send-otp', ['phone' => $user->phone]);
        $response->assertSessionHasErrors('phone');
    }

    public function test_send_otp_fails_for_rejected_resident(): void
    {
        $user = User::factory()->rejected()->create(['role' => 'resident']);
        $response = $this->post('/login/send-otp', ['phone' => $user->phone]);
        $response->assertSessionHasErrors('phone');
    }

    // ---------------------------------------------------------------
    // Register
    // ---------------------------------------------------------------

    public function test_register_page_is_accessible(): void
    {
        $zone = Zone::create(['name' => 'Zone 1']);

        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Auth/AuthScreen'));
    }

    public function test_resident_can_register(): void
    {
        $zone = Zone::create(['name' => 'Zone 1']);

        $response = $this->post('/register', [
            'name' => 'Juan dela Cruz',
            'phone' => '09171234567',
            'address' => 'Purok 1',
            'zone_id' => $zone->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'phone' => '09171234567',
            'role' => 'resident',
            'status' => 'pending',
        ]);
    }

    public function test_register_fails_with_duplicate_phone(): void
    {
        User::factory()->create(['phone' => '09171234567']);

        $zone = Zone::create(['name' => 'Zone 1']);

        $response = $this->post('/register', [
            'name' => 'Another User',
            'phone' => '09171234567',
            'address' => 'Purok 2',
            'zone_id' => $zone->id,
        ]);

        $response->assertSessionHasErrors('phone');
    }

    // ---------------------------------------------------------------
    // Logout
    // ---------------------------------------------------------------

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->official()->create();
        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect();
        $this->assertGuest();
    }
}
