<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\StaffDocument;
use App\Models\StaffDocumentType;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_verify_staff_by_number_without_private_fields(): void
    {
        $staffMember = StaffMember::factory()->approved()->for(Business::factory()->verified()->state([
            'business_name' => 'Seme Freight Ltd',
        ]))->create([
            'full_name' => 'Ibrahim Musa',
            'nin' => '12345678901',
            'passport_number' => 'A99887766',
            'phone' => '08031112222',
            'email' => 'staff-private@example.test',
        ]);

        $this->post(route('public.verification.search'), ['query' => $staffMember->staff_number])
            ->assertOk()
            ->assertSeeText('Staff Clearance Verified')
            ->assertSeeText('Ibrahim Musa')
            ->assertSeeText('Seme Freight Ltd')
            ->assertSeeText($staffMember->staff_number)
            ->assertDontSeeText('12345678901')
            ->assertDontSeeText('A99887766')
            ->assertDontSeeText('08031112222')
            ->assertDontSeeText('staff-private@example.test');
    }

    public function test_expired_staff_clearance_is_reported_as_not_valid(): void
    {
        $staffMember = StaffMember::factory()->approved()->create([
            'status' => 'expired',
            'verification_expires_at' => now()->subDay(),
        ]);

        $this->post(route('public.verification.search'), ['query' => $staffMember->staff_number])
            ->assertOk()
            ->assertSeeText('Staff Clearance Not Valid');
    }

    public function test_staff_clearance_is_not_valid_while_their_business_is_not_verified(): void
    {
        $business = Business::factory()->approved()->create(['status' => 'expired']);
        $staffMember = StaffMember::factory()->approved()->for($business)->create();

        $this->post(route('public.verification.search'), ['query' => $staffMember->staff_number])
            ->assertOk()
            ->assertSeeText('Staff Clearance Not Valid');
    }

    public function test_unknown_staff_number_returns_the_no_match_result(): void
    {
        $this->post(route('public.verification.search'), ['query' => 'NBCCI-STF-2026-999999'])
            ->assertOk()
            ->assertSeeText('No matching business or certificate was found.');
    }

    public function test_business_verification_still_works_alongside_staff_lookup(): void
    {
        $business = Business::factory()->verified()->create(['business_name' => 'Porto Novo Foods']);

        $this->post(route('public.verification.search'), ['query' => $business->registry_number])
            ->assertOk()
            ->assertSeeText('Business Verified')
            ->assertSeeText('Porto Novo Foods');
    }

    public function test_government_official_finds_staff_by_name_and_number(): void
    {
        $official = User::factory()->create(['role' => 'government_official']);
        $staffMember = StaffMember::factory()->approved()->create(['full_name' => 'Zainab Yusuf']);

        $this->actingAs($official)
            ->post(route('government.search.execute'), ['query' => 'Zainab'])
            ->assertOk()
            ->assertSeeText('Staff Matches (1)')
            ->assertSeeText('Zainab Yusuf');

        $this->actingAs($official)
            ->post(route('government.search.execute'), ['query' => $staffMember->staff_number])
            ->assertOk()
            ->assertSeeText('Zainab Yusuf');
    }

    public function test_government_official_sees_clearance_and_the_view_is_audit_logged(): void
    {
        $official = User::factory()->create(['role' => 'government_official']);
        $staffMember = StaffMember::factory()->approved()->for(Business::factory()->verified())->create();
        StaffDocument::factory()->accepted()->for($staffMember)->create();

        $this->actingAs($official)
            ->get(route('government.staff.show', $staffMember))
            ->assertOk()
            ->assertSeeText('Cleared to cross')
            ->assertSeeText($staffMember->full_name);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'government_view_staff',
            'user_id' => $official->id,
        ]);
    }

    public function test_government_official_sees_staff_that_is_not_cleared(): void
    {
        $official = User::factory()->create(['role' => 'government_official']);
        $staffMember = StaffMember::factory()->submitted()->create();

        $this->actingAs($official)
            ->get(route('government.staff.show', $staffMember))
            ->assertOk()
            ->assertSeeText('Not cleared');
    }

    public function test_forbids_owners_from_the_government_staff_page(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->for(Business::factory()->approved()->for($owner))->create();

        $this->actingAs($owner)
            ->get(route('government.staff.show', $staffMember))
            ->assertForbidden();
    }

    public function test_expire_command_lapses_only_staff_past_their_expiry(): void
    {
        $lapsed = StaffMember::factory()->approved()->create(['verification_expires_at' => now()->subDay()]);
        $current = StaffMember::factory()->approved()->create(['verification_expires_at' => now()->addMonth()]);
        $draft = StaffMember::factory()->create();

        $this->artisan('verifications:expire')->assertSuccessful();

        $this->assertSame('expired', $lapsed->refresh()->status);
        $this->assertSame('approved', $current->refresh()->status);
        $this->assertSame('draft', $draft->refresh()->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'staff.verification_expired']);
    }

    public function test_super_admin_manages_staff_document_types(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->post(route('super-admin.staff-document-types.store'), [
                'name' => 'Passport Photograph',
                'is_required' => 1,
                'status' => 'active',
            ])
            ->assertRedirect(route('super-admin.staff-document-types.index'));

        $type = StaffDocumentType::where('name', 'Passport Photograph')->firstOrFail();

        $this->actingAs($superAdmin)
            ->get(route('super-admin.staff-document-types.index'))
            ->assertOk()
            ->assertSeeText('Passport Photograph');

        $this->actingAs($superAdmin)
            ->put(route('super-admin.staff-document-types.update', $type), [
                'name' => 'Passport Photograph',
                'is_required' => 0,
                'status' => 'inactive',
            ])
            ->assertRedirect(route('super-admin.staff-document-types.index'));

        $type->refresh();

        $this->assertFalse($type->is_required);
        $this->assertSame('inactive', $type->status);

        $this->actingAs($superAdmin)
            ->delete(route('super-admin.staff-document-types.destroy', $type))
            ->assertRedirect(route('super-admin.staff-document-types.index'));

        $this->assertModelMissing($type);
    }

    public function test_super_admin_can_open_the_staff_document_type_forms(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $type = StaffDocumentType::factory()->create(['name' => 'Work Permit']);

        $this->actingAs($superAdmin)
            ->get(route('super-admin.staff-document-types.create'))
            ->assertOk()
            ->assertSee('name="is_required"', false);

        $this->actingAs($superAdmin)
            ->get(route('super-admin.staff-document-types.edit', $type))
            ->assertOk()
            ->assertSee('value="Work Permit"', false);
    }

    public function test_staff_document_type_in_use_cannot_be_deleted(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $type = StaffDocumentType::factory()->create();
        StaffDocument::factory()->create(['staff_document_type_id' => $type->id]);

        $this->actingAs($superAdmin)
            ->delete(route('super-admin.staff-document-types.destroy', $type))
            ->assertSessionHas('error', 'This staff document type cannot be deleted while documents use it.');

        $this->assertModelExists($type);
    }

    public function test_forbids_admins_from_managing_staff_document_types(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('super-admin.staff-document-types.index'))
            ->assertForbidden();
    }
}
