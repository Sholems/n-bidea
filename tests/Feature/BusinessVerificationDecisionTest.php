<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessProfile;
use App\Models\BusinessVerificationCheck;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\BusinessVerificationDecisionNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BusinessVerificationDecisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_verify_an_approved_business_after_a_phone_call(): void
    {
        Notification::fake();
        Setting::set('verification_validity_months', 18);
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $business = Business::factory()->approved()->create();

        $this->actingAs($superAdmin)
            ->post(route('super-admin.businesses.verification.store', $business), [
                'decision' => 'verified',
                'method' => 'phone_call',
                'note' => 'Spoke with the authorised director and confirmed the submitted records.',
            ])
            ->assertRedirect(route('admin.businesses.show', $business))
            ->assertSessionHas('success');

        $business->refresh();

        $this->assertSame('verified', $business->status);
        $this->assertTrue($business->is_verified);
        $this->assertNotNull($business->verified_at);
        $this->assertTrue($business->verification_expires_at->between(now()->addMonths(17), now()->addMonths(19)));
        $this->assertNotNull($business->certificate);
        $this->assertSame('active', $business->certificate->status);
        $this->assertDatabaseHas('business_verification_checks', [
            'business_id' => $business->id,
            'super_admin_id' => $superAdmin->id,
            'method' => 'phone_call',
            'decision' => 'verified',
        ]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'business.verification.verified']);
        Notification::assertSentTo($business->user, BusinessVerificationDecisionNotification::class);
    }

    public function test_super_admin_can_record_an_unsuccessful_site_visit_without_granting_the_mark(): void
    {
        Notification::fake();
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $business = Business::factory()->approved()->create();

        $this->actingAs($superAdmin)
            ->post(route('super-admin.businesses.verification.store', $business), [
                'decision' => 'not_verified',
                'method' => 'site_visit',
                'note' => 'The operating address could not be confirmed during the scheduled visit.',
            ])
            ->assertRedirect(route('admin.businesses.show', $business));

        $business->refresh();

        $this->assertSame('approved', $business->status);
        $this->assertFalse($business->is_verified);
        $this->assertNull($business->certificate);
        $this->assertDatabaseHas('business_verification_checks', [
            'business_id' => $business->id,
            'method' => 'site_visit',
            'decision' => 'not_verified',
        ]);
    }

    public function test_admin_and_business_owner_cannot_make_a_verification_decision(): void
    {
        $business = Business::factory()->approved()->create();

        foreach (['admin', 'business_owner'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->post(route('super-admin.businesses.verification.store', $business), [
                    'decision' => 'verified',
                    'method' => 'phone_call',
                    'note' => 'This user is not authorised to grant verification.',
                ])
                ->assertForbidden();
        }

        $this->assertSame(0, BusinessVerificationCheck::count());
        $this->assertSame('approved', $business->refresh()->status);
    }

    public function test_verification_requires_a_supported_method_decision_and_detailed_note(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $business = Business::factory()->approved()->create();

        $this->actingAs($superAdmin)
            ->post(route('super-admin.businesses.verification.store', $business), [
                'decision' => 'approved',
                'method' => 'email',
                'note' => 'short',
            ])
            ->assertInvalid(['decision', 'method', 'note']);

        $this->assertSame(0, BusinessVerificationCheck::count());
    }

    public function test_unapproved_business_cannot_be_verified(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $business = Business::factory()->submitted()->create();

        $this->actingAs($superAdmin)
            ->post(route('super-admin.businesses.verification.store', $business), [
                'decision' => 'verified',
                'method' => 'site_visit',
                'note' => 'Completed an inspection of the operating premises and records.',
            ])
            ->assertForbidden();

        $this->assertSame('submitted', $business->refresh()->status);
    }

    public function test_public_profile_shows_the_verification_mark_only_for_verified_businesses(): void
    {
        $approvedProfile = BusinessProfile::factory()->approved()->create([
            'business_id' => Business::factory()->approved()->create([
                'business_name' => 'Approved Trade Company',
            ])->id,
        ]);
        $verifiedProfile = BusinessProfile::factory()->approved()->create([
            'business_id' => Business::factory()->verified()->create([
                'business_name' => 'Verified Trade Company',
            ])->id,
        ]);

        $this->get(route('public.directory.show', $approvedProfile))
            ->assertOk()
            ->assertSeeText('Registered Business Listing')
            ->assertSeeText('Registration approved')
            ->assertDontSeeText('Verified by NB-CCI');

        $this->get(route('public.directory.show', $verifiedProfile))
            ->assertOk()
            ->assertSeeText('Verified Business Listing')
            ->assertSeeText('Verified by NB-CCI');
    }
}
