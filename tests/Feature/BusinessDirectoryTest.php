<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessInquiry;
use App\Models\BusinessProfile;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_business_cannot_submit_a_public_profile(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create([
            'business_name' => 'Draft Corridor Company',
            'status' => 'draft',
        ]);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.profile.store', $business), $this->validProfilePayload())
            ->assertForbidden();

        $this->assertDatabaseMissing('business_profiles', [
            'business_id' => $business->id,
        ]);
    }

    public function test_business_owner_can_submit_profile_for_their_verified_business(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create([
            'business_name' => 'Seme Agro Logistics',
        ]);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.profile.store', $business), $this->validProfilePayload([
                'services' => 'Cold-chain aggregation and bonded logistics.',
                'trade_interests' => 'Seeking distributors in Benin and Togo.',
            ]))
            ->assertRedirect(route('business-owner.businesses.profile.show', $business))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('business_profiles', [
            'business_id' => $business->id,
            'services' => 'Cold-chain aggregation and bonded logistics.',
            'trade_interests' => 'Seeking distributors in Benin and Togo.',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_approve_or_reject_business_profiles(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'active',
        ]);
        $profile = BusinessProfile::factory()->pending()->create([
            'contact_preference' => 'Use portal introduction only.',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.business-profiles.update', $profile), [
                'decision' => 'approved',
                'admin_note' => 'Safe to publish.',
            ])
            ->assertRedirect(route('admin.business-profiles.show', $profile))
            ->assertSessionHas('success');

        $profile->refresh();

        $this->assertSame('approved', $profile->status);
        $this->assertSame('Use portal introduction only.', $profile->contact_preference);
        $this->assertNotNull($profile->approved_at);
        $this->assertSame($admin->id, $profile->approved_by);
    }

    public function test_guests_can_browse_only_approved_directory_profiles_without_private_fields(): void
    {
        $sector = Sector::factory()->create(['name' => 'Logistics']);
        $publishedProfile = BusinessProfile::factory()->approved()->create([
            'business_id' => Business::factory()->approved()->create([
                'business_name' => 'Cotonou Corridor Logistics',
                'sector_id' => $sector->id,
                'nin' => '12345678901',
                'email' => 'owner@example.test',
                'phone' => '08030000000',
                'contact_person_email' => 'private-contact@example.test',
            ])->id,
            'services' => 'Cross-border haulage and warehousing.',
            'operating_locations' => 'Lagos, Seme, Cotonou',
        ]);
        BusinessProfile::factory()->pending()->create([
            'business_id' => Business::factory()->approved()->create([
                'business_name' => 'Pending Export House',
            ])->id,
        ]);

        $this->get(route('public.directory.index', ['query' => 'Cotonou']))
            ->assertOk()
            ->assertSeeText('Cotonou Corridor Logistics')
            ->assertSeeText('Cross-border haulage and warehousing.')
            ->assertDontSeeText('Pending Export House')
            ->assertDontSeeText('12345678901')
            ->assertDontSeeText('owner@example.test')
            ->assertDontSeeText('08030000000')
            ->assertDontSeeText('private-contact@example.test');

        $this->get(route('public.directory.show', $publishedProfile))
            ->assertOk()
            ->assertSeeText('Cotonou Corridor Logistics')
            ->assertSeeText('Lagos, Seme, Cotonou')
            ->assertSeeText('Logistics')
            ->assertDontSeeText('12345678901')
            ->assertDontSeeText('owner@example.test')
            ->assertDontSeeText('08030000000')
            ->assertDontSeeText('private-contact@example.test');
    }

    public function test_authenticated_user_can_submit_directory_inquiry_without_exposing_owner_contact(): void
    {
        $requester = User::factory()->create([
            'name' => 'Ada Exporter',
            'email' => 'ada@example.test',
        ]);
        $profile = BusinessProfile::factory()->approved()->create();

        $this->actingAs($requester)
            ->post(route('public.directory.inquiries.store', $profile), [
                'requester_company' => 'Ada Export Company',
                'interest_type' => 'distributor',
                'message' => 'We want to discuss distribution across the Seme corridor.',
            ])
            ->assertRedirect(route('public.directory.show', $profile))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('business_inquiries', [
            'business_profile_id' => $profile->id,
            'requester_id' => $requester->id,
            'requester_name' => 'Ada Exporter',
            'requester_email' => 'ada@example.test',
            'requester_company' => 'Ada Export Company',
            'interest_type' => 'distributor',
            'status' => 'pending',
        ]);

        $this->get(route('public.directory.show', $profile))
            ->assertOk()
            ->assertDontSeeText($profile->business->email)
            ->assertDontSeeText($profile->business->phone);
    }

    public function test_guests_are_redirected_before_creating_directory_inquiries(): void
    {
        $profile = BusinessProfile::factory()->approved()->create();

        $this->post(route('public.directory.inquiries.store', $profile), [
            'requester_company' => 'Guest Company',
            'interest_type' => 'buyer',
            'message' => 'We want to buy corridor logistics services.',
        ])->assertRedirect(route('login'));

        $this->assertSame(0, BusinessInquiry::count());
    }

    private function validProfilePayload(array $overrides = []): array
    {
        return array_merge([
            'summary' => 'Verified corridor business supporting Nigeria-Benin trade.',
            'services' => 'Export support, customs documentation, and aggregation.',
            'operating_locations' => 'Badagry, Seme, Cotonou',
            'trade_interests' => 'Looking for buyers, suppliers, and logistics partners.',
            'certifications' => 'NB-CCI verified business',
            'website' => 'https://example.test',
            'contact_preference' => 'Send introductions through the NB-CCI portal.',
        ], $overrides);
    }
}
