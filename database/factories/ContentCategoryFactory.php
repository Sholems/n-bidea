<?php

namespace Database\Factories;

use App\Models\ContentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ContentCategory>
 */
class ContentCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Corridor Intelligence',
            'AfCFTA Guides',
            'Export Readiness',
            'Customs and Compliance',
            'Investment Opportunities',
            'Sector Briefs',
            'Policy Updates',
            'Women and Youth Enterprise',
            'Trade Missions',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'status' => 'active',
        ];
    }
}
