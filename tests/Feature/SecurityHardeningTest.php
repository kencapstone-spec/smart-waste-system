<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_storage_route_prevents_directory_traversal(): void
    {
        // Traversal attempts must never disclose sensitive files like .env
        $response = $this->get('/storage/%2E%2E%2F%2E%2E%2F%2E%2E%2F%2E%2E%2F.env');
        $this->assertStringNotContainsString('DB_PASSWORD', (string) $response->getContent());
        $this->assertStringNotContainsString('APP_KEY', (string) $response->getContent());
    }

    public function test_cron_route_rejects_invalid_or_missing_secret(): void
    {
        $response = $this->get('/run-background-jobs');
        $response->assertStatus(403);

        $responseWithWrongSecret = $this->get('/run-background-jobs?secret=wrong_secret_12345');
        $responseWithWrongSecret->assertStatus(403);
    }

    public function test_deactivated_user_cannot_access_protected_routes(): void
    {
        $deactivatedResident = User::factory()->create([
            'role' => 'resident',
            'status' => 'rejected',
        ]);

        $response = $this->actingAs($deactivatedResident)->get(route('resident.schedules.index'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
