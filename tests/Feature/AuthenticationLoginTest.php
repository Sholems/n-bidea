<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AuthenticationLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_super_admin_can_login_and_reach_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post(route('login'), [
            'email' => 'admin@nb-cci.gov.ng',
            'password' => 'password',
        ])
            ->assertRedirect(route('super-admin.dashboard'))
            ->assertSessionHasNoErrors();
    }

    public function test_seeded_business_owner_can_login_and_reach_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post(route('login'), [
            'email' => 'business@example.com',
            'password' => 'password',
        ])
            ->assertRedirect(route('business-owner.dashboard'))
            ->assertSessionHasNoErrors();
    }

    public function test_database_cache_store_is_available_for_login_rate_limiting(): void
    {
        Cache::store('database')->put('login-rate-limit-check', true, 60);

        $this->assertTrue(Cache::store('database')->get('login-rate-limit-check'));
    }

    public function test_login_page_shows_demo_accounts_only_in_local_environment(): void
    {
        Config::set('app.env', 'local');

        $this->get(route('login'))
            ->assertOk()
            ->assertSeeText('Demo logins')
            ->assertSeeText('business@example.com / password');

        Config::set('app.env', 'production');

        $this->get(route('login'))
            ->assertOk()
            ->assertDontSeeText('Demo logins')
            ->assertDontSeeText('business@example.com / password');
    }
}
