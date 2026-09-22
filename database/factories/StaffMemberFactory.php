<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\StaffMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffMember>
 */
class StaffMemberFactory extends Factory
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
            'staff_number' => null,
            'full_name' => fake()->name(),
            'job_title' => 'Driver',
            'phone' => fake()->numerify('080########'),
            'email' => fake()->unique()->safeEmail(),
            'nationality' => 'Nigerian',
            'date_of_birth' => fake()->date('Y-m-d', '-21 years'),
            'nin' => fake()->numerify('###########'),
            'passport_number' => fake()->bothify('A########'),
            'status' => 'draft',
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'submitted',
        ]);
    }

    public function correctionRequired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'correction_required',
            'review_note' => 'Passport photo is unreadable.',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'staff_number' => 'NBCCI-STF-'.now()->format('Y').'-'.fake()->unique()->numerify('######'),
            'status' => 'approved',
            'verified_at' => now(),
            'verification_expires_at' => now()->addYear(),
        ]);
    }
}
