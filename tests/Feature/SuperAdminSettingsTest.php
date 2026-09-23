<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\DocumentType;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_form_posts_to_the_update_route_without_a_method_override(): void
    {
        $superAdmin = $this->superAdmin();
        Setting::set('certification_fee', '25000');

        $this->actingAs($superAdmin)
            ->get(route('super-admin.settings.index'))
            ->assertOk()
            ->assertSee('action="'.route('super-admin.settings.update').'"', false)
            ->assertSee('name="settings[certification_fee]"', false)
            ->assertDontSee('name="_method"', false);
    }

    public function test_super_admin_saves_several_settings_at_once(): void
    {
        $superAdmin = $this->superAdmin();
        Setting::set('certification_fee', '25000');
        Setting::set('renewal_fee', '15000');
        Setting::set('contact_email', 'old@example.test');

        $this->actingAs($superAdmin)
            ->post(route('super-admin.settings.update'), [
                'settings' => [
                    'certification_fee' => '30000',
                    'renewal_fee' => '18000.50',
                    'contact_email' => 'info@example.test',
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('success', '3 setting(s) updated successfully.');

        $this->assertSame('30000', Setting::get('certification_fee'));
        $this->assertSame('18000.50', Setting::get('renewal_fee'));
        $this->assertSame('info@example.test', Setting::get('contact_email'));
        $this->assertSame(3, AuditLog::where('action', 'setting.updated')->count());
    }

    public function test_only_changed_settings_are_written_and_audit_logged(): void
    {
        $superAdmin = $this->superAdmin();
        Setting::set('certification_fee', '25000');
        Setting::set('renewal_fee', '15000');

        $this->actingAs($superAdmin)
            ->post(route('super-admin.settings.update'), [
                'settings' => [
                    'certification_fee' => '25000',
                    'renewal_fee' => '16000',
                ],
            ])
            ->assertSessionHas('success', '1 setting(s) updated successfully.');

        $this->assertSame(1, AuditLog::where('action', 'setting.updated')->count());
    }

    public function test_reports_when_nothing_changed(): void
    {
        $superAdmin = $this->superAdmin();
        Setting::set('certification_fee', '25000');

        $this->actingAs($superAdmin)
            ->post(route('super-admin.settings.update'), [
                'settings' => ['certification_fee' => '25000'],
            ])
            ->assertSessionHas('success', 'No changes to save.');

        $this->assertSame(0, AuditLog::where('action', 'setting.updated')->count());
    }

    public function test_a_fee_saved_here_is_the_amount_raised_when_a_business_is_submitted(): void
    {
        $superAdmin = $this->superAdmin();
        Setting::set('certification_fee', '25000');

        $this->actingAs($superAdmin)
            ->post(route('super-admin.settings.update'), [
                'settings' => ['certification_fee' => '40000'],
            ]);

        DocumentType::query()->update(['status' => 'inactive']);
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'draft']);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.submit', $business))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('fees', [
            'business_id' => $business->id,
            'fee_type' => 'certification',
            'amount' => '40000.00',
        ]);
    }

    public function test_rejects_a_setting_key_that_does_not_exist(): void
    {
        $superAdmin = $this->superAdmin();
        Setting::set('certification_fee', '25000');

        $this->actingAs($superAdmin)
            ->post(route('super-admin.settings.update'), [
                'settings' => ['made_up_key' => 'value'],
            ])
            ->assertInvalid(['settings']);

        $this->assertNull(Setting::where('key', 'made_up_key')->first());
    }

    public function test_rejects_a_fee_that_is_not_a_non_negative_number(): void
    {
        $superAdmin = $this->superAdmin();
        Setting::set('certification_fee', '25000');
        Setting::set('renewal_fee', '15000');

        $this->actingAs($superAdmin)
            ->post(route('super-admin.settings.update'), [
                'settings' => [
                    'certification_fee' => 'twenty thousand',
                    'renewal_fee' => '-5',
                ],
            ])
            ->assertInvalid(['settings.certification_fee', 'settings.renewal_fee']);

        $this->assertSame('25000', Setting::get('certification_fee'));
        $this->assertSame('15000', Setting::get('renewal_fee'));
    }

    public function test_rejects_an_invalid_verification_validity(): void
    {
        $superAdmin = $this->superAdmin();
        Setting::set('verification_validity_months', '12');

        $this->actingAs($superAdmin)
            ->post(route('super-admin.settings.update'), [
                'settings' => ['verification_validity_months' => '0'],
            ])
            ->assertInvalid(['settings.verification_validity_months']);

        $this->assertSame('12', Setting::get('verification_validity_months'));
    }

    public function test_validation_errors_are_shown_on_the_settings_page(): void
    {
        $superAdmin = $this->superAdmin();
        Setting::set('certification_fee', '25000');

        $this->actingAs($superAdmin)
            ->from(route('super-admin.settings.index'))
            ->followingRedirects()
            ->post(route('super-admin.settings.update'), [
                'settings' => ['certification_fee' => 'abc'],
            ])
            ->assertOk()
            ->assertSeeText('Settings were not saved')
            ->assertSeeText('certification fee');
    }

    public function test_requires_a_settings_payload(): void
    {
        $superAdmin = $this->superAdmin();

        $this->actingAs($superAdmin)
            ->post(route('super-admin.settings.update'), [])
            ->assertInvalid(['settings']);
    }

    public function test_forbids_admins_and_owners_from_saving_settings(): void
    {
        Setting::set('certification_fee', '25000');

        foreach (['admin', 'business_owner', 'government_official'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->post(route('super-admin.settings.update'), [
                    'settings' => ['certification_fee' => '1'],
                ])
                ->assertForbidden();
        }

        $this->assertSame('25000', Setting::get('certification_fee'));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->post(route('super-admin.settings.update'), [
            'settings' => ['certification_fee' => '1'],
        ])->assertRedirect(route('login'));
    }

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }
}
