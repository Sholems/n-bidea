<?php

namespace Database\Factories;

use App\Models\ContentCategory;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Publication>
 */
class PublicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'content_category_id' => ContentCategory::factory(),
            'uploaded_by' => User::factory()->state([
                'role' => 'super_admin',
                'account_status' => 'active',
            ]),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->sentence(16),
            'file_path' => 'publications/'.Str::slug($title).'.pdf',
            'original_file_name' => Str::slug($title).'.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'audience' => 'public',
            'status' => 'draft',
            'published_at' => null,
            'download_count' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}
