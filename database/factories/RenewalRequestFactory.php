<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\RenewalRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RenewalRequest>
 */
class RenewalRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory()->verified(),
            'requested_by' => User::factory(),
            'status' => 'pending',
            'admin_id' => null,
            'admin_note' => null,
            'reason' => fake()->optional()->sentence(),
            'previous_expiry_date' => now(),
            'new_expiry_date' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'approved',
            'admin_id' => User::factory()->state(['role' => 'admin']),
            'new_expiry_date' => now()->addYear(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'rejected',
            'admin_id' => User::factory()->state(['role' => 'admin']),
            'admin_note' => 'Documentation did not match the renewal request.',
        ]);
    }
}
