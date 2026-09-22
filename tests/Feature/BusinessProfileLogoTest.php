<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessProfileLogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_upload_a_logo_and_view_it_while_profile_is_pending(): void
    {
        Storage::fake('private');
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.profile.store', $business), $this->profilePayload([
                'logo' => UploadedFile::fake()->image('company-logo.png', 600, 600),
            ]))
            ->assertRedirect(route('business-owner.businesses.profile.show', $business));

        $profile = $business->profile()->firstOrFail();

        $this->assertSame('pending', $profile->status);
        $this->assertSame('image/png', $profile->logo_mime_type);
        Storage::disk('private')->assertExists($profile->logo_path);

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.profile.logo', $business))
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
    }

    public function test_public_logo_is_available_only_for_an_approved_profile(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('business-logos/1/logo.png', 'image-content');
        $profile = BusinessProfile::factory()->pending()->create([
            'logo_path' => 'business-logos/1/logo.png',
            'logo_mime_type' => 'image/png',
        ]);

        $this->get(route('public.directory.logo', $profile))->assertNotFound();

        $profile->update(['status' => 'approved', 'approved_at' => now()]);

        $this->get(route('public.directory.logo', $profile))
            ->assertOk()
            ->assertHeader('content-type', 'image/png');
    }

    public function test_replacing_a_logo_deletes_the_previous_cloud_object(): void
    {
        Storage::fake('private');
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();
        $profile = BusinessProfile::factory()->approved()->create([
            'business_id' => $business->id,
            'logo_path' => 'business-logos/old-logo.png',
            'logo_mime_type' => 'image/png',
        ]);
        Storage::disk('private')->put($profile->logo_path, 'old-image');

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.profile.store', $business), $this->profilePayload([
                'logo' => UploadedFile::fake()->image('replacement.jpg', 500, 500),
            ]))
            ->assertSessionHasNoErrors();

        $profile->refresh();

        Storage::disk('private')->assertMissing('business-logos/old-logo.png');
        Storage::disk('private')->assertExists($profile->logo_path);
    }

    public function test_other_owner_cannot_view_or_replace_a_business_logo(): void
    {
        Storage::fake('private');
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();
        $profile = BusinessProfile::factory()->approved()->create([
            'business_id' => $business->id,
            'logo_path' => 'business-logos/private.png',
        ]);
        Storage::disk('private')->put($profile->logo_path, 'image');

        $this->actingAs($otherOwner)
            ->get(route('business-owner.businesses.profile.logo', $business))
            ->assertForbidden();

        $this->actingAs($otherOwner)
            ->post(route('business-owner.businesses.profile.store', $business), $this->profilePayload([
                'logo' => UploadedFile::fake()->image('intruder.png'),
            ]))
            ->assertForbidden();

        $this->assertSame('business-logos/private.png', $profile->refresh()->logo_path);
    }

    public function test_logo_must_be_a_supported_image_within_the_size_limit(): void
    {
        Storage::fake('private');
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.profile.store', $business), $this->profilePayload([
                'logo' => UploadedFile::fake()->create('logo.svg', 100, 'image/svg+xml'),
            ]))
            ->assertInvalid(['logo']);

        $this->assertDatabaseMissing('business_profiles', ['business_id' => $business->id]);
    }

    private function profilePayload(array $overrides = []): array
    {
        return array_merge([
            'summary' => 'Approved corridor business serving Nigeria and Benin markets.',
            'services' => 'Cross-border logistics and export support services.',
            'operating_locations' => 'Lagos, Seme, and Cotonou',
            'trade_interests' => 'Regional distribution partnerships.',
            'certifications' => 'CAC registered',
            'website' => 'https://example.test',
            'contact_preference' => 'Use the portal introduction service.',
        ], $overrides);
    }
}
