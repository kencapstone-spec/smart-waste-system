<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class BackgroundJobsTest extends TestCase
{
    public function test_background_jobs_endpoint_requires_valid_secret(): void
    {
        Config::set('app.cron_secret', 'ValidSecret123');

        // No secret provided
        $response1 = $this->get('/run-background-jobs');
        $response1->assertStatus(403);

        // Invalid secret provided
        $response2 = $this->get('/run-background-jobs?secret=WrongSecret');
        $response2->assertStatus(403);

        // Correct secret provided
        $response3 = $this->get('/run-background-jobs?secret=ValidSecret123');
        $response3->assertStatus(200);
        $response3->assertJsonStructure([
            'status',
            'timestamp',
        ]);
    }
}
