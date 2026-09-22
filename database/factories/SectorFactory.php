<?php

namespace Database\Factories;

use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sector>
 */
class SectorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Agribusiness',
                'Manufacturing',
                'Logistics',
                'Energy',
                'Tourism',
                'Digital Trade',
            ]),
            'status' => 'active',
        ];
    }
}
