<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Fee;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessOwnerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_status_counts_for_only_the_owners_own_businesses(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        Business::factory()->for($owner)->create(['status' => 'draft']);
        Business::factory()->approved()->for($owner)->create();
        Business::factory()->verified()->for($owner)->create();
        Business::factory()->verified()->for($stranger)->create();

        $this->actingAs($owner)
            ->get(route('business-owner.dashboard'))
            ->assertOk()
            ->assertViewHas('counts', function (array $counts): bool {
                return $counts['total'] === 3
                    && $counts['draft'] === 1
                    && $counts['approved'] === 1
                    && $counts['verified'] === 1;
            });
    }

    public function test_warns_about_verification_expiring_within_thirty_days(): void
    {
        $owner = User::factory()->create();
        $soon = Business::factory()->verified()->for($owner)->create([
            'business_name' => 'Expiring Soon Traders',
            'verification_expires_at' => now()->addDays(10),
        ]);
        Business::factory()->verified()->for($owner)->create([
            'business_name' => 'Comfortably Verified Ltd',
            'verification_expires_at' => now()->addMonths(6),
        ]);

        $this->actingAs($owner)
            ->get(route('business-owner.dashboard'))
            ->assertOk()
            ->assertSeeText('Verification expiring soon')
            ->assertViewHas('expiringBusinesses', function ($businesses) use ($soon): bool {
                return $businesses->count() === 1 && $businesses->first()->is($soon);
            });
    }

    public function test_shows_staff_and_fee_summaries_scoped_to_the_owners_businesses(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();
        StaffMember::factory()->submitted()->for($business)->create();
        StaffMember::factory()->approved()->for($business)->create();
        Fee::factory()->for($business)->create(['amount' => 25000, 'payment_status' => 'unpaid']);
        Fee::factory()->for($business)->create(['amount' => 15000, 'payment_status' => 'pending_confirmation']);

        $this->actingAs($owner)
            ->get(route('business-owner.dashboard'))
            ->assertOk()
            ->assertViewHas('staffCounts', function (array $counts): bool {
                return $counts['total'] === 2 && $counts['pending_review'] === 1 && $counts['approved'] === 1;
            })
            ->assertViewHas('feeCounts', function (array $counts): bool {
                return $counts['unpaid'] === 1 && $counts['pending_confirmation'] === 1 && $counts['unpaid_amount'] === 25000.0;
            });
    }

    public function test_lists_the_owners_businesses_with_a_link_to_each(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['business_name' => 'Cotonou Freight Partners']);

        $this->actingAs($owner)
            ->get(route('business-owner.dashboard'))
            ->assertOk()
            ->assertSeeText('Cotonou Freight Partners')
            ->assertSee(route('business-owner.businesses.show', $business), false);
    }
}
