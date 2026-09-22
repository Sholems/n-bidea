<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_presents_the_n_bidea_platform_positioning(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSeeText('Nigeria-Benin Business Integration, Development and Economic Advancement Programme')
            ->assertSeeText('Register Your Business')
            ->assertSeeText('Verify a Business')
            ->assertSeeText('Explore Investment Opportunities')
            ->assertSeeText('50,000+')
            ->assertSeeText('20,000+')
            ->assertSeeText('US$2B')
            ->assertSeeText('Latest corridor insights')
            ->assertSeeText('Trade & Investment');
    }

    public function test_core_public_pages_are_available_to_guests(): void
    {
        $pages = [
            'public.about' => 'Nigeria-Benin Chamber of Commerce and Industry',
            'public.n-bidea' => 'N-BIDEA',
            'public.priority-sectors' => 'Priority Sectors',
            'public.afcfta-ecowas' => 'AfCFTA and ECOWAS',
            'public.contact' => 'Contact NB-CCI',
        ];

        foreach ($pages as $routeName => $expectedText) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertSeeText($expectedText);
        }
    }

    public function test_private_filesystem_disk_is_configured_for_sensitive_uploads(): void
    {
        $this->assertSame('local', config('filesystems.disks.private.driver'));
        $this->assertSame(storage_path('app/private'), config('filesystems.disks.private.root'));
    }
}
