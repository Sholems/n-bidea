<?php

namespace Database\Factories;

use App\Models\StaffDocumentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffDocumentType>
 */
class StaffDocumentTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'is_required' => true,
            'status' => 'active',
        ];
    }

    public function optional(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_required' => false,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'inactive',
        ]);
    }
}
