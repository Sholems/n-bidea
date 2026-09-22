<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Fee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fee>
 */
class FeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'fee_type' => 'certification',
            'amount' => 25000,
            'payment_status' => 'unpaid',
            'payment_reference' => null,
            'proof_file_path' => null,
            'confirmed_by' => null,
            'confirmed_at' => null,
        ];
    }

    public function renewal(): static
    {
        return $this->state(fn (array $attributes): array => [
            'fee_type' => 'renewal',
        ]);
    }

    public function pendingConfirmation(): static
    {
        return $this->state(fn (array $attributes): array => [
            'payment_status' => 'pending_confirmation',
            'payment_reference' => 'REF-'.fake()->unique()->numerify('######'),
            'proof_file_path' => 'payment-proofs/proof.pdf',
        ]);
    }

    public function paid(): static
    {
        return $this->pendingConfirmation()->state(fn (array $attributes): array => [
            'payment_status' => 'paid',
            'confirmed_at' => now(),
        ]);
    }
}
