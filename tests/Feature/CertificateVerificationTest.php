<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Certificate;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_approving_a_business_creates_an_active_certificate(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'account_status' => 'active',
        ]);
        $business = Business::factory()->submitted()->create([
            'business_name' => 'Akin Border Logistics',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.businesses.show', $business))
            ->post(route('admin.businesses.review', $business), [
                'decision' => 'approved',
                'note' => 'Documents accepted.',
            ])
            ->assertRedirect(route('admin.businesses.show', $business));

        $business->refresh();
        $certificate = $business->certificate;

        $this->assertNotNull($certificate);
        $this->assertSame('active', $certificate->status);
        $this->assertSame($business->registry_number, $certificate->business->registry_number);
        $this->assertTrue($certificate->expires_at->isFuture());
        $this->assertStringContainsString('/verify/', $certificate->qr_payload);
        $this->assertDatabaseHas('business_profiles', [
            'business_id' => $business->id,
            'status' => 'approved',
        ]);

        $this->get(route('public.directory.index', ['query' => $business->registry_number]))
            ->assertOk()
            ->assertSeeText('Akin Border Logistics')
            ->assertSeeText($business->registry_number);
    }

    public function test_guest_can_verify_certificate_by_number_without_private_fields(): void
    {
        $sector = Sector::factory()->create(['name' => 'Logistics']);
        $business = Business::factory()->approved()->create([
            'business_name' => 'Seme Trade Services',
            'sector_id' => $sector->id,
            'nin' => '12345678901',
            'email' => 'owner@example.test',
            'phone' => '08030000000',
        ]);
        $certificate = Certificate::factory()->for($business)->active()->create([
            'certificate_number' => 'NBCCI-CERT-2026-000001',
            'verification_code' => 'VER-ABC12345',
        ]);

        $this->post(route('public.verification.search'), [
            'query' => $certificate->certificate_number,
        ])
            ->assertOk()
            ->assertSeeText('Certificate Verified')
            ->assertSeeText('Seme Trade Services')
            ->assertSeeText($business->registry_number)
            ->assertSeeText('Logistics')
            ->assertSeeText('Valid')
            ->assertDontSeeText('12345678901')
            ->assertDontSeeText('owner@example.test')
            ->assertDontSeeText('08030000000');
    }

    public function test_guest_can_verify_certificate_by_registry_number_and_code_url(): void
    {
        $business = Business::factory()->approved()->create([
            'business_name' => 'Porto Novo Foods',
        ]);
        $certificate = Certificate::factory()->for($business)->active()->create([
            'verification_code' => 'VER-PORTO2026',
        ]);

        $this->post(route('public.verification.search'), [
            'query' => $business->registry_number,
        ])
            ->assertOk()
            ->assertSeeText('Certificate Verified')
            ->assertSeeText('Porto Novo Foods');

        $this->get(route('public.verification.code', $certificate->verification_code))
            ->assertOk()
            ->assertSeeText('Certificate Verified')
            ->assertSeeText($certificate->certificate_number);
    }

    public function test_guest_can_verify_approved_business_by_registry_number_without_certificate(): void
    {
        $business = Business::factory()->approved()->create([
            'business_name' => 'John Business Enterprises',
            'registry_number' => 'NBCCI-NG-2026-000001',
            'nin' => '12345678901',
            'email' => 'owner@example.test',
            'phone' => '08030000000',
        ]);

        $this->post(route('public.verification.search'), [
            'query' => $business->registry_number,
        ])
            ->assertOk()
            ->assertSeeText('Business Verified')
            ->assertSeeText('John Business Enterprises')
            ->assertSeeText('NBCCI-NG-2026-000001')
            ->assertSeeText('Certificate Pending Issuance')
            ->assertDontSeeText('12345678901')
            ->assertDontSeeText('owner@example.test')
            ->assertDontSeeText('08030000000');
    }

    public function test_revoked_and_unknown_certificates_return_limited_results(): void
    {
        $business = Business::factory()->approved()->create([
            'business_name' => 'Lagos Benin Agro',
        ]);
        $certificate = Certificate::factory()->for($business)->revoked()->create([
            'certificate_number' => 'NBCCI-CERT-2026-000099',
        ]);

        $this->post(route('public.verification.search'), [
            'query' => $certificate->certificate_number,
        ])
            ->assertOk()
            ->assertSeeText('Certificate Not Valid')
            ->assertSeeText('Lagos Benin Agro')
            ->assertDontSeeText('Certificate Verified');

        $this->post(route('public.verification.search'), [
            'query' => 'NBCCI-CERT-2026-404404',
        ])
            ->assertOk()
            ->assertSeeText('No matching business or certificate was found.');
    }

    public function test_business_owner_can_view_only_their_own_certificate(): void
    {
        $owner = User::factory()->create();
        $otherOwner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create([
            'business_name' => 'Cotonou Corridor Merchants',
        ]);
        $certificate = Certificate::factory()->for($business)->active()->create();

        $this->actingAs($owner)
            ->get(route('business-owner.certificates.show', $certificate))
            ->assertOk()
            ->assertSeeText('Business Certificate')
            ->assertSeeText('Cotonou Corridor Merchants')
            ->assertSeeText($certificate->certificate_number);

        $this->actingAs($otherOwner)
            ->get(route('business-owner.certificates.show', $certificate))
            ->assertForbidden();
    }
}
