<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\BusinessProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessProfile>
 */
class BusinessProfileFactory extends Factory
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
            'summary' => fake()->paragraph(),
            'services' => fake()->sentence(),
            'operating_locations' => 'Badagry, Seme, Cotonou',
            'trade_interests' => fake()->sentence(),
            'certifications' => 'NB-CCI registered business',
            'website' => fake()->optional()->url(),
            'contact_preference' => 'Use portal introduction only.',
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'admin_note' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'approved',
            'approved_by' => User::factory()->state([
                'role' => 'admin',
                'account_status' => 'active',
            ]),
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'rejected',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }
}
