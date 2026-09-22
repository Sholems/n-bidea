<?php

namespace Database\Seeders;

use App\Models\StaffDocumentType;
use Illuminate\Database\Seeder;

class StaffDocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $documentTypes = [
            ['name' => 'Passport Photograph', 'description' => 'Recent passport-sized photograph', 'is_required' => true],
            ['name' => 'National ID / NIN Slip', 'description' => 'NIN slip or national identity card', 'is_required' => true],
            ['name' => 'Passport or Travel Document', 'description' => 'Data page of an international passport or ECOWAS travel document', 'is_required' => true],
            ['name' => 'Employment Letter', 'description' => 'Letter from the business confirming employment', 'is_required' => false],
            ['name' => 'Other Supporting Document', 'description' => 'Any other supporting document', 'is_required' => false],
        ];

        foreach ($documentTypes as $documentType) {
            StaffDocumentType::updateOrCreate(
                ['name' => $documentType['name']],
                $documentType + ['status' => 'active'],
            );
        }
    }
}
