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
                'name' => 'CAC Registration Certificate',
                'description' => 'Official Certificate of Incorporation, Business Name Registration, or another valid registration document issued by the Corporate Affairs Commission.',
                'country_code' => 'NG',
                'is_required' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Tax/NRS Certificate',
                'description' => 'Valid tax registration document issued by the relevant revenue authority and showing the business tax or revenue registration number.',
                'country_code' => 'NG',
                'is_required' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Proof of Address',
                'description' => 'Recent utility bill, tenancy agreement, property document, bank statement, or another official record confirming the business address.',
                'country_code' => 'NG',
                'is_required' => true,
                'status' => 'active',
            ],
            [
                'name' => 'NIN Document',
                'description' => 'Valid National Identification Number slip belonging to the business owner, director, or authorised representative.',
                'country_code' => 'NG',
                'is_required' => true,
                'status' => 'active',
            ],
            [
                'name' => 'Other Supporting Document',
                'description' => 'Additional licence, permit, certificate, regulatory approval, business profile, or supporting evidence confirming the business operations and eligibility.',
                'country_code' => 'NG',
                'is_required' => false,
                'status' => 'active',
            ],
        ];

        foreach ($documentTypes as $documentType) {
            DocumentType::updateOrCreate(
                ['name' => $documentType['name']],
                $documentType,
            );
        }
    }
}
