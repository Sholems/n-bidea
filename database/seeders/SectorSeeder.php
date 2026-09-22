<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            'Agriculture',
            'Manufacturing',
            'Trading/Import-Export',
            'Oil & Gas',
            'Technology/ICT',
            'Financial Services',
            'Real Estate',
            'Transportation',
            'Healthcare',
            'Education',
            'Construction',
            'Mining',
            'Hospitality/Tourism',
            'Telecommunications',
            'Retail/Wholesale',
            'Professional Services',
            'Energy',
            'Fisheries',
            'Livestock',
            'Solid Minerals',
        ];

        foreach ($sectors as $name) {
            Sector::updateOrCreate(
                ['name' => $name],
                ['status' => 'active'],
            );
        }
    }
}
