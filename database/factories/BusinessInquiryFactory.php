<?php

namespace Database\Factories;

use App\Models\BusinessInquiry;
use App\Models\BusinessProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessInquiry>
 */
class BusinessInquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_profile_id' => BusinessProfile::factory()->approved(),
            'requester_id' => User::factory(),
            'requester_name' => fake()->name(),
            'requester_email' => fake()->safeEmail(),
            'requester_phone' => fake()->optional()->phoneNumber(),
            'requester_company' => fake()->company(),
            'interest_type' => 'buyer',
            'message' => fake()->paragraph(),
            'status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
            'admin_note' => null,
        ];
    }
}
