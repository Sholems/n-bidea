<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GovernmentDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_lookups_appear_in_the_officials_recent_checks(): void
    {
        $official = User::factory()->create(['role' => 'government_official']);
        $staffMember = StaffMember::factory()->approved()->create(['full_name' => 'Amaka Ibe']);

        $this->actingAs($official)
            ->get(route('government.staff.show', $staffMember));

        $this->actingAs($official)
            ->get(route('government.dashboard'))
            ->assertOk()
            ->assertSeeText('Staff viewed')
            ->assertViewHas('counts', fn (array $counts) => $counts['today'] === 1 && $counts['total'] === 1);
    }

    public function test_business_lookups_appear_in_the_officials_recent_checks(): void
    {
        $official = User::factory()->create(['role' => 'government_official']);
        $business = Business::factory()->approved()->create();

        $this->actingAs($official)->get(route('government.businesses.show', $business));
        $this->actingAs($official)->post(route('government.businesses.record-check', $business));

        $this->actingAs($official)
            ->get(route('government.dashboard'))
            ->assertOk()
            ->assertSeeText('Business viewed')
            ->assertSeeText('Verification recorded')
            ->assertViewHas('counts', fn (array $counts) => $counts['total'] === 2);
    }

    public function test_only_counts_the_authenticated_officials_own_checks(): void
    {
        $official = User::factory()->create(['role' => 'government_official']);
        $otherOfficial = User::factory()->create(['role' => 'government_official']);
        $business = Business::factory()->approved()->create();

        $this->actingAs($otherOfficial)->get(route('government.businesses.show', $business));

        $this->actingAs($official)
            ->get(route('government.dashboard'))
            ->assertOk()
            ->assertViewHas('counts', fn (array $counts) => $counts['total'] === 0);
    }

    public function test_unrelated_audit_actions_do_not_appear_as_checks(): void
    {
        $official = User::factory()->create(['role' => 'government_official']);

        $this->actingAs($official)->post(route('government.search.execute'), ['query' => 'anything']);

        $this->actingAs($official)
            ->get(route('government.dashboard'))
            ->assertOk()
            ->assertViewHas('counts', fn (array $counts) => $counts['total'] === 0);
    }
}
