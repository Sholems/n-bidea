<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            AgencySeeder::class,
            SectorSeeder::class,
            ContentCategorySeeder::class,
            DocumentTypeSeeder::class,
            StaffDocumentTypeSeeder::class,
            UserSeeder::class,
            BusinessDirectoryProfileSeeder::class,
        ]);
    }
}
