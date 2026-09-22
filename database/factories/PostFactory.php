<?php

namespace Database\Factories;

use App\Models\ContentCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
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
            'author_id' => User::factory()->state([
                'role' => 'super_admin',
                'account_status' => 'active',
            ]),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(12),
            'body' => fake()->paragraphs(3, true),
            'featured_image_path' => null,
            'audience' => 'public',
            'status' => 'draft',
            'published_at' => null,
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
