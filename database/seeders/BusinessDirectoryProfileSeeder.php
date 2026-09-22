<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Services\BusinessDirectoryService;
use Illuminate\Database\Seeder;

class BusinessDirectoryProfileSeeder extends Seeder
{
    public function run(BusinessDirectoryService $businessDirectoryService): void
    {
        Business::query()
            ->whereIn('status', ['approved', 'verified'])
            ->chunkById(100, function ($businesses) use ($businessDirectoryService): void {
                foreach ($businesses as $business) {
                    $businessDirectoryService->publishDefaultListing($business);
                }
            });
    }
}
