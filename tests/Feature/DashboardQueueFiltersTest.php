<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Fee;
use App\Models\RenewalRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardQueueFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_queue_filters_match_dashboard_definitions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $submitted = Business::factory()->create(['business_name' => 'Submitted Registry Company', 'status' => 'submitted']);
        $underReview = Business::factory()->create(['business_name' => 'Reviewing Registry Company', 'status' => 'under_review']);
        $active = Business::factory()->verified()->create([
            'business_name' => 'Active Verified Company',
            'verification_expires_at' => now()->addMonth(),
        ]);
        $lapsed = Business::factory()->verified()->create([
            'business_name' => 'Lapsed Verified Company',
            'verification_expires_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.businesses.index', ['queue' => 'pending_review']))
            ->assertOk()
            ->assertSeeText($submitted->business_name)
            ->assertSeeText($underReview->business_name)
            ->assertDontSeeText($active->business_name);

        $this->get(route('admin.businesses.index', ['verification' => 'active']))
            ->assertOk()
            ->assertSeeText($active->business_name)
            ->assertDontSeeText($lapsed->business_name);

        $this->get(route('admin.businesses.index', ['verification' => 'expired']))
            ->assertOk()
            ->assertSeeText($lapsed->business_name)
            ->assertDontSeeText($active->business_name);
    }

    public function test_expiring_soon_filter_excludes_later_and_lapsed_verifications(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $expiring = Business::factory()->verified()->create([
            'business_name' => 'Expiring Soon Company',
            'verification_expires_at' => now()->addDays(10),
        ]);
        $later = Business::factory()->verified()->create([
            'business_name' => 'Later Expiry Company',
            'verification_expires_at' => now()->addDays(60),
        ]);
        $lapsed = Business::factory()->verified()->create([
            'business_name' => 'Already Lapsed Company',
            'verification_expires_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.businesses.index', ['verification' => 'expiring_soon']))
            ->assertOk()
            ->assertSeeText($expiring->business_name)
            ->assertDontSeeText($later->business_name)
            ->assertDontSeeText($lapsed->business_name);
    }

    public function test_fee_and_renewal_queue_filters_only_show_the_requested_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pendingFee = Fee::factory()->pendingConfirmation()->create();
        $paidFee = Fee::factory()->paid()->create();
        $pendingRenewal = RenewalRequest::factory()->create();
        $approvedRenewal = RenewalRequest::factory()->approved()->create();

        $this->actingAs($admin)
            ->get(route('admin.fees.index', ['status' => 'pending_confirmation']))
            ->assertOk()
            ->assertSeeText($pendingFee->business->business_name)
            ->assertDontSeeText($paidFee->business->business_name);

        $this->get(route('admin.renewals.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSeeText($pendingRenewal->business->business_name)
            ->assertDontSeeText($approvedRenewal->business->business_name);
    }
}
