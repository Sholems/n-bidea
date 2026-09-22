<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
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
            'certificate_number' => 'NBCCI-CERT-'.now()->format('Y').'-'.fake()->unique()->numerify('######'),
            'verification_code' => 'VER-'.Str::upper(Str::random(12)),
            'status' => 'active',
            'issued_by' => null,
            'issued_at' => now(),
            'expires_at' => now()->addYear(),
            'revoked_at' => null,
            'qr_payload' => 'http://localhost/verify/'.Str::upper(Str::random(12)),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'active',
            'expires_at' => now()->addYear(),
            'revoked_at' => null,
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);
    }
}
