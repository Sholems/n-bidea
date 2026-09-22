<?php

namespace Database\Seeders;

use App\Models\Agency;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
        $agencies = [
            [
                'name' => 'Nigerian Border Officials',
                'type' => 'federal',
                'contact_email' => 'border@nb-cci.gov.ng',
                'contact_phone' => '+234-800-001-0001',
                'status' => 'active',
            ],
            [
                'name' => 'Nigerian Customs Service',
                'type' => 'federal',
                'contact_email' => 'customs@nb-cci.gov.ng',
                'contact_phone' => '+234-800-002-0002',
                'status' => 'active',
            ],
            [
                'name' => 'Federal Ministry of Industry, Trade and Investment',
                'type' => 'federal',
                'contact_email' => 'fmiti@nb-cci.gov.ng',
                'contact_phone' => '+234-800-003-0003',
                'status' => 'active',
            ],
            [
                'name' => 'Nigeria Immigration Service',
                'type' => 'federal',
                'contact_email' => 'immigration@nb-cci.gov.ng',
                'contact_phone' => '+234-800-004-0004',
                'status' => 'active',
            ],
        ];

        foreach ($agencies as $agency) {
            Agency::updateOrCreate(
                ['contact_email' => $agency['contact_email']],
                $agency,
            );
        }
    }
}
