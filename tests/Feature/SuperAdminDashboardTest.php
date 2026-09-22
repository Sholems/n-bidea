<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessVerificationCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_the_awaiting_verification_queue(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $awaiting = Business::factory()->approved()->create(['business_name' => 'Seme Crossing Traders']);
        Business::factory()->verified()->create(['business_name' => 'Already Verified Co']);
        Business::factory()->create(['status' => 'draft', 'business_name' => 'Untouched Draft Co']);

        $this->actingAs($superAdmin)
            ->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Seme Crossing Traders')
            ->assertDontSeeText('Already Verified Co')
            ->assertDontSeeText('Untouched Draft Co')
            ->assertViewHas('reviewQueues', fn (array $q) => $q['awaiting_verification'] === 1);
    }

    public function test_shows_recent_verification_checks(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        BusinessVerificationCheck::factory()->create([
            'super_admin_id' => $superAdmin->id,
            'decision' => 'verified',
            'method' => 'site_visit',
            'business_id' => Business::factory()->verified()->create(['business_name' => 'Checked Business Ltd']),
        ]);

        $this->actingAs($superAdmin)
            ->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Checked Business Ltd')
            ->assertSeeText('Site visit');
    }

    public function test_forbids_admins_from_the_super_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('super-admin.dashboard'))
            ->assertForbidden();
    }
}
