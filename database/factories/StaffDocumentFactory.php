<?php

namespace Database\Factories;

use App\Models\StaffDocument;
use App\Models\StaffDocumentType;
use App\Models\StaffMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffDocument>
 */
class StaffDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_member_id' => StaffMember::factory(),
            'staff_document_type_id' => StaffDocumentType::factory(),
            'file_path' => 'staff-documents/1/'.fake()->uuid().'.pdf',
            'original_file_name' => 'document.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 2048,
            'status' => 'pending',
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'accepted',
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'rejected',
            'admin_note' => 'Unreadable scan.',
            'reviewed_at' => now(),
        ]);
    }
}
