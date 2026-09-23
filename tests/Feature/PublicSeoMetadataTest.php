<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSeoMetadataTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_search_and_social_sharing_metadata(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<meta name="description" content="Connect with verified businesses', false)
            ->assertSee('<meta property="og:title" content="N-BIDEA Platform - NB-CCI">', false)
            ->assertSee('<meta property="og:image" content="'.asset(config('seo.default_image')).'">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
            ->assertSee('<link rel="icon" href="'.asset('favicon.ico').'" sizes="any">', false)
            ->assertSee('<link rel="manifest" href="'.asset('site.webmanifest').'">', false);
    }

    public function test_public_business_profile_uses_its_own_seo_description(): void
    {
        $profile = BusinessProfile::factory()->approved()->create([
            'business_id' => Business::factory()->verified()->create([
                'business_name' => 'Seme Export Partners',
            ])->id,
            'summary' => 'Trusted agricultural exporter connecting Nigerian producers with buyers throughout Benin.',
        ]);

        $this->get(route('public.directory.show', $profile))
            ->assertOk()
            ->assertSee('<meta name="description" content="Trusted agricultural exporter connecting Nigerian producers with buyers throughout Benin.">', false)
            ->assertSee('<meta property="og:title" content="Seme Export Partners - NB-CCI Directory">', false);
    }

    public function test_authentication_pages_are_not_indexed(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
            ->assertSee(asset('favicon.ico'), false);
    }

    public function test_favicon_assets_are_present_and_non_empty(): void
    {
        $assets = [
            'favicon.ico',
            'favicon-16x16.png',
            'favicon-32x32.png',
            'apple-touch-icon.png',
            'android-chrome-192x192.png',
            'android-chrome-512x512.png',
            'site.webmanifest',
        ];

        foreach ($assets as $asset) {
            $path = public_path($asset);

            $this->assertFileExists($path);
            $this->assertGreaterThan(0, filesize($path));
        }
    }
}
