<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'registry_number' => null,
            'business_name' => fake()->company(),
            'trading_name' => fake()->optional()->company(),
            'registration_number' => strtoupper(fake()->bothify('BN-######')),
            'cac_number' => fake()->optional()->bothify('CAC-######'),
            'nrs_number' => fake()->optional()->bothify('NRS-######'),
            'nin' => fake()->optional()->numerify('###########'),
            'business_type' => 'limited_liability_company',
            'country_code' => 'NG',
            'sector_id' => Sector::factory(),
            'description' => fake()->sentence(),
            'address' => fake()->streetAddress(),
            'state' => 'Lagos',
            'lga' => 'Badagry',
            'city' => 'Lagos',
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->companyEmail(),
            'website' => fake()->optional()->url(),
            'contact_person_name' => fake()->name(),
            'contact_person_phone' => fake()->phoneNumber(),
            'contact_person_email' => fake()->unique()->safeEmail(),
            'trade_activity' => 'Cross-border trade',
            'border_route' => 'Seme-Krake',
            'status' => 'draft',
            'verified_at' => null,
            'verification_expires_at' => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'submitted',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'registry_number' => 'NBCCI-NG-'.now()->format('Y').'-'.fake()->unique()->numerify('######'),
            'status' => 'approved',
            'verified_at' => null,
            'verification_expires_at' => null,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'registry_number' => 'NBCCI-NG-'.now()->format('Y').'-'.fake()->unique()->numerify('######'),
            'status' => 'verified',
            'verified_at' => now(),
            'verification_expires_at' => now()->addYear(),
        ]);
    }
}
