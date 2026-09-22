<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\BusinessVerificationCheck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessVerificationCheck>
 */
class BusinessVerificationCheckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory()->approved(),
            'super_admin_id' => User::factory()->state(['role' => 'super_admin']),
            'method' => 'phone_call',
            'decision' => 'verified',
            'note' => fake()->sentence(),
            'checked_at' => now(),
            'expires_at' => now()->addYear(),
        ];
    }
}
