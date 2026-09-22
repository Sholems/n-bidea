<?php

namespace Database\Seeders;

use App\Models\ContentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Corridor Intelligence',
            'AfCFTA Guides',
            'Export Readiness',
            'Customs and Compliance',
            'Investment Opportunities',
            'Sector Briefs',
            'Policy Updates',
            'Women and Youth Enterprise',
            'Trade Missions',
        ] as $name) {
            ContentCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => "{$name} resources for the Nigeria-Benin trade corridor.",
                    'status' => 'active',
                ],
            );
        }
    }
}
