<?php

namespace App\Services;

use App\Models\Business;
use App\Models\BusinessProfile;
use App\Models\User;

class BusinessDirectoryService
{
    public function publishDefaultListing(Business $business, ?User $approver = null): BusinessProfile
    {
        $business->loadMissing('sector');

        $locations = collect([$business->city, $business->state, $business->border_route])
            ->filter()
            ->unique()
            ->implode(', ');

        return $business->profile()->firstOrCreate(
            ['business_id' => $business->id],
            [
                'summary' => $business->description ?: "{$business->business_name} is an NB-CCI registered business.",
                'services' => $business->trade_activity ?: $business->description ?: 'Registered business services',
                'operating_locations' => $locations ?: 'Nigeria-Benin trade corridor',
                'certifications' => null,
                'website' => $business->website,
                'contact_preference' => 'Request an introduction through the NB-CCI directory.',
                'status' => 'approved',
                'approved_by' => $approver?->id,
                'approved_at' => now(),
            ],
        );
    }
}
