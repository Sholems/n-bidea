<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\StaffDocument;
use App\Models\StaffDocumentType;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_adds_staff_to_approved_business_as_a_draft(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $response = $this->actingAs($owner)
            ->post(route('business-owner.businesses.staff.store', $business), $this->validPayload([
                'full_name' => 'Ibrahim Musa',
            ]));

        $staffMember = StaffMember::where('full_name', 'Ibrahim Musa')->firstOrFail();

        $response->assertRedirect(route('business-owner.staff.show', $staffMember))
            ->assertSessionHas('success');

        $this->assertSame('draft', $staffMember->status);
        $this->assertSame($business->id, $staffMember->business_id);
        $this->assertNull($staffMember->staff_number);
        $this->assertDatabaseHas('audit_logs', ['action' => 'staff.created']);
    }

    public function test_business_can_have_many_staff_members(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();
        StaffMember::factory()->for($business)->count(3)->sequence(
            ['full_name' => 'Driver One'],
            ['full_name' => 'Driver Two'],
            ['full_name' => 'Driver Three'],
        )->create();

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.staff.index', $business))
            ->assertOk()
            ->assertSeeText('Driver One')
            ->assertSeeText('Driver Two')
            ->assertSeeText('Driver Three');

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.staff.store', $business), $this->validPayload());

        $this->assertSame(4, $business->staffMembers()->count());
    }

    public function test_owner_can_open_the_add_and_edit_forms(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();
        $staffMember = StaffMember::factory()->for($business)->create(['full_name' => 'Editable Person']);

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.staff.create', $business))
            ->assertOk()
            ->assertSee('name="full_name"', false);

        $this->actingAs($owner)
            ->get(route('business-owner.staff.edit', $staffMember))
            ->assertOk()
            ->assertSee('value="Editable Person"', false);
    }

    public function test_forbids_adding_staff_to_a_business_that_is_not_approved(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'submitted']);

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.staff.create', $business))
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.staff.store', $business), $this->validPayload())
            ->assertForbidden();

        $this->assertSame(0, StaffMember::count());
    }

    public function test_forbids_adding_staff_to_another_owners_business(): void
    {
        $stranger = User::factory()->create();
        $business = Business::factory()->approved()->create();

        $this->actingAs($stranger)
            ->post(route('business-owner.businesses.staff.store', $business), $this->validPayload())
            ->assertForbidden();

        $this->assertSame(0, StaffMember::count());
    }

    public function test_guest_is_redirected_to_login_when_adding_staff(): void
    {
        $business = Business::factory()->approved()->create();

        $this->post(route('business-owner.businesses.staff.store', $business), $this->validPayload())
            ->assertRedirect(route('login'));
    }

    public function test_requires_the_core_staff_fields(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.staff.store', $business), [])
            ->assertInvalid(['full_name', 'job_title', 'phone', 'nationality']);
    }

    public function test_rejects_a_nin_that_is_not_eleven_digits(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.staff.store', $business), $this->validPayload(['nin' => '12345']))
            ->assertInvalid(['nin']);
    }

    public function test_rejects_a_date_of_birth_in_the_future(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.staff.store', $business), $this->validPayload([
                'date_of_birth' => now()->addDay()->format('Y-m-d'),
            ]))
            ->assertInvalid(['date_of_birth']);
    }

    public function test_owner_can_view_their_staff_member(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->for(Business::factory()->approved()->for($owner))->create([
            'full_name' => 'Aisha Bello',
        ]);

        $this->actingAs($owner)
            ->get(route('business-owner.staff.show', $staffMember))
            ->assertOk()
            ->assertSeeText('Aisha Bello')
            ->assertSeeText('Upload Staff Document');
    }

    public function test_business_staff_page_explains_where_documents_are_uploaded(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.staff.index', $business))
            ->assertOk()
            ->assertSeeText('Staff Documents')
            ->assertSeeText('Open a staff record to upload');
    }

    public function test_forbids_viewing_another_owners_staff_member(): void
    {
        $stranger = User::factory()->create();
        $staffMember = StaffMember::factory()->create();

        $this->actingAs($stranger)
            ->get(route('business-owner.staff.show', $staffMember))
            ->assertForbidden();
    }

    public function test_forbids_listing_staff_of_another_owners_business(): void
    {
        $stranger = User::factory()->create();
        $business = Business::factory()->approved()->create();

        $this->actingAs($stranger)
            ->get(route('business-owner.businesses.staff.index', $business))
            ->assertForbidden();
    }

    public function test_owner_updates_a_draft_staff_member(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->for(Business::factory()->approved()->for($owner))->create();

        $this->actingAs($owner)
            ->put(route('business-owner.staff.update', $staffMember), $this->validPayload(['job_title' => 'Warehouse Lead']))
            ->assertRedirect(route('business-owner.staff.show', $staffMember));

        $this->assertSame('Warehouse Lead', $staffMember->refresh()->job_title);
    }

    public function test_forbids_editing_a_submitted_staff_member(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->submitted()->for(Business::factory()->approved()->for($owner))->create([
            'job_title' => 'Driver',
        ]);

        $this->actingAs($owner)
            ->get(route('business-owner.staff.edit', $staffMember))
            ->assertForbidden();

        $this->actingAs($owner)
            ->put(route('business-owner.staff.update', $staffMember), $this->validPayload(['job_title' => 'Manager']))
            ->assertForbidden();

        $this->assertSame('Driver', $staffMember->refresh()->job_title);
    }

    public function test_owner_removes_a_draft_staff_member_and_their_files(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('staff-documents/1/photo.pdf', 'file');
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();
        $staffMember = StaffMember::factory()->for($business)->create();
        StaffDocument::factory()->for($staffMember)->create(['file_path' => 'staff-documents/1/photo.pdf']);

        $this->actingAs($owner)
            ->delete(route('business-owner.staff.destroy', $staffMember))
            ->assertRedirect(route('business-owner.businesses.staff.index', $business));

        $this->assertModelMissing($staffMember);
        $this->assertSame(0, StaffDocument::count());
        Storage::disk('private')->assertMissing('staff-documents/1/photo.pdf');
    }

    public function test_forbids_removing_a_staff_member_once_submitted(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->submitted()->for(Business::factory()->approved()->for($owner))->create();

        $this->actingAs($owner)
            ->delete(route('business-owner.staff.destroy', $staffMember))
            ->assertForbidden();

        $this->assertModelExists($staffMember);
    }

    public function test_owner_submits_staff_once_every_required_document_is_uploaded(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->for(Business::factory()->approved()->for($owner))->create();
        $required = StaffDocumentType::factory()->create();
        StaffDocumentType::factory()->optional()->create();
        StaffDocumentType::factory()->inactive()->create();
        StaffDocument::factory()->for($staffMember)->create(['staff_document_type_id' => $required->id]);

        $this->actingAs($owner)
            ->post(route('business-owner.staff.submit', $staffMember))
            ->assertSessionHas('success');

        $this->assertSame('submitted', $staffMember->refresh()->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'staff.submitted']);
    }

    public function test_blocks_submission_while_a_required_document_is_missing(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->for(Business::factory()->approved()->for($owner))->create();
        StaffDocumentType::factory()->create();

        $this->actingAs($owner)
            ->post(route('business-owner.staff.submit', $staffMember))
            ->assertSessionHas('error', 'Please upload all required documents before submitting.');

        $this->assertSame('draft', $staffMember->refresh()->status);
    }

    public function test_blocks_submission_when_the_business_is_no_longer_approved(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create(['status' => 'expired']);
        $staffMember = StaffMember::factory()->for($business)->create();

        $this->actingAs($owner)
            ->post(route('business-owner.staff.submit', $staffMember))
            ->assertSessionHas('error', 'The business must be approved before staff can be submitted.');

        $this->assertSame('draft', $staffMember->refresh()->status);
    }

    public function test_rejects_plain_submit_for_staff_awaiting_correction(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->correctionRequired()->for(Business::factory()->approved()->for($owner))->create();

        $this->actingAs($owner)
            ->post(route('business-owner.staff.submit', $staffMember))
            ->assertSessionHas('error', 'Only draft staff records can be submitted for review.');

        $this->assertSame('correction_required', $staffMember->refresh()->status);
    }

    public function test_correction_response_resubmits_the_staff_member(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->correctionRequired()->for(Business::factory()->approved()->for($owner))->create();

        $this->actingAs($owner)
            ->post(route('business-owner.staff.correction-response', $staffMember), ['note' => 'Uploaded a clearer photo.'])
            ->assertSessionHas('success');

        $staffMember->refresh();

        $this->assertSame('submitted', $staffMember->status);
        $this->assertSame('Uploaded a clearer photo.', $staffMember->correction_response);
    }

    public function test_correction_response_requires_a_note(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->correctionRequired()->for(Business::factory()->approved()->for($owner))->create();

        $this->actingAs($owner)
            ->post(route('business-owner.staff.correction-response', $staffMember), [])
            ->assertInvalid(['note']);
    }

    public function test_rejects_correction_response_when_no_correction_was_requested(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->for(Business::factory()->approved()->for($owner))->create();

        $this->actingAs($owner)
            ->post(route('business-owner.staff.correction-response', $staffMember), ['note' => 'Not needed.'])
            ->assertSessionHas('error', 'This staff member is not awaiting corrections.');

        $this->assertSame('draft', $staffMember->refresh()->status);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Ibrahim Musa',
            'job_title' => 'Driver',
            'phone' => '08031234567',
            'email' => 'ibrahim@example.test',
            'nationality' => 'Nigerian',
            'date_of_birth' => '1990-05-12',
            'nin' => '12345678901',
            'passport_number' => 'A12345678',
        ], $overrides);
    }
}
