<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'         => fake()->name(),
            'phone'        => '09' . fake()->unique()->numerify('#########'),
            'role'         => 'resident',
            'status'       => 'active',
            'address'      => fake()->address(),
            'remember_token' => Str::random(10),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(['role' => 'super_admin', 'status' => 'active']);
    }

    public function official(): static
    {
        return $this->state(['role' => 'barangay_official', 'status' => 'active']);
    }

    public function personnel(): static
    {
        return $this->state(['role' => 'personnel', 'status' => 'active']);
    }

    public function resident(): static
    {
        return $this->state(['role' => 'resident', 'status' => 'active']);
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function rejected(): static
    {
        return $this->state(['status' => 'rejected']);
    }
}
