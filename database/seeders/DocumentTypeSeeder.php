<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $documentTypes = [
            [
                'name' => 'CAC Document',
                'description' => 'CAC Registration Certificate',
                'is_required' => true,
                'status' => 'active',
            ],
            [
                'name' => 'NRS Document',
                'description' => 'Tax Registration Certificate',
                'is_required' => true,
                'status' => 'active',
            ],
            [
                'name' => 'NIN Document',
                'description' => 'National Identity Number',
                'is_required' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Proof of Address',
                'description' => 'Utility bill or tenancy agreement',
                'is_required' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Other Supporting Document',
                'description' => 'Other Supporting Document',
                'is_required' => false,
                'status' => 'active',
            ],
        ];

        foreach ($documentTypes as $documentType) {
            DocumentType::create($documentType);
        }
    }
}
