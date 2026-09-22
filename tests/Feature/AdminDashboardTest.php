<?php

namespace Tests\Feature;

use App\Models\BusinessProfile;
use App\Models\Fee;
use App\Models\RenewalRequest;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_operational_queue_counts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Fee::factory()->pendingConfirmation()->create();
        RenewalRequest::factory()->create(['status' => 'pending']);
        StaffMember::factory()->submitted()->create();
        BusinessProfile::factory()->pending()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('operationalCounts', function (array $counts): bool {
                return $counts['fees_pending_confirmation'] === 1
                    && $counts['renewals_pending'] === 1
                    && $counts['staff_pending_review'] === 1
                    && $counts['profiles_pending_review'] === 1;
            });
    }

    public function test_quick_links_are_present(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.staff.index'), false)
            ->assertSee(route('admin.business-profiles.index'), false)
            ->assertSee(route('admin.fees.index'), false)
            ->assertSee(route('admin.renewals.index'), false);
    }

    public function test_forbids_business_owners_from_the_admin_dashboard(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
