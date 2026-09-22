<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\DocumentType;
use App\Models\Fee;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_submits_draft_business_and_status_becomes_submitted(): void
    {
        $this->disableDocumentRequirements();

        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'draft']);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.submit', $business))
            ->assertSessionHas('success');

        $this->assertSame('submitted', $business->refresh()->status);
    }

    public function test_submission_is_blocked_while_a_required_document_is_missing(): void
    {
        DocumentType::create(['name' => 'CAC Document', 'is_required' => true, 'status' => 'active']);
        Setting::set('certification_fee', '25000');
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'draft']);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.submit', $business))
            ->assertSessionHas('error', 'Please upload all required documents before submitting.');

        $this->assertSame('draft', $business->refresh()->status);
        $this->assertSame(0, Fee::count());
    }

    public function test_rejects_resubmitting_an_approved_business(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.submit', $business))
            ->assertSessionHas('error', 'Only draft businesses can be submitted for review.');

        $this->assertSame('approved', $business->refresh()->status);
        $this->assertNotNull($business->registry_number);
    }

    public function test_forbids_submitting_another_owners_business(): void
    {
        $stranger = User::factory()->create();
        $business = Business::factory()->create(['status' => 'draft']);

        $this->actingAs($stranger)
            ->post(route('business-owner.businesses.submit', $business))
            ->assertForbidden();

        $this->assertSame('draft', $business->refresh()->status);
    }

    public function test_rejects_plain_submit_while_business_is_awaiting_correction(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'correction_required']);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.submit', $business))
            ->assertSessionHas('error', 'Only draft businesses can be submitted for review.');

        $this->assertSame('correction_required', $business->refresh()->status);
    }

    public function test_correction_response_still_resubmits_a_business_awaiting_correction(): void
    {
        $this->disableDocumentRequirements();

        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'correction_required']);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.correction-response', $business), [
                'note' => 'Uploaded the corrected certificate.',
            ])
            ->assertSessionHas('success');

        $business->refresh();

        $this->assertSame('submitted', $business->status);
        $this->assertSame('Uploaded the corrected certificate.', $business->correction_response);
    }

    public function test_forbids_editing_a_business_while_it_is_under_active_review(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'submitted']);

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.edit', $business))
            ->assertForbidden();

        $this->actingAs($owner)
            ->put(route('business-owner.businesses.update', $business), ['business_name' => 'New Name'])
            ->assertForbidden();

        $this->assertNotSame('New Name', $business->refresh()->business_name);
    }

    public function test_owner_can_open_edit_form_for_a_draft_business(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'draft']);

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.edit', $business))
            ->assertOk();
    }

    public function test_owner_can_open_edit_form_for_an_approved_or_verified_business(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->verified()->for($owner)->create();

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.edit', $business))
            ->assertOk();
    }

    public function test_editing_a_verified_business_sends_it_back_for_review_and_drops_the_verified_mark(): void
    {
        $owner = User::factory()->create();
        $registryNumber = 'NBCCI-NG-2026-000123';
        $business = Business::factory()->verified()->for($owner)->create([
            'phone' => '08010000000',
            'registry_number' => $registryNumber,
        ]);

        $this->actingAs($owner)
            ->put(route('business-owner.businesses.update', $business), ['phone' => '08099999999'])
            ->assertRedirect()
            ->assertSessionHas('success', 'Business updated. Your changes have been sent back for review, and the verified mark is on hold until a reviewer confirms them.');

        $business->refresh();

        $this->assertSame('08099999999', $business->phone);
        $this->assertSame('submitted', $business->status);
        $this->assertNull($business->verified_at);
        $this->assertNull($business->verification_expires_at);
        $this->assertSame($registryNumber, $business->registry_number);
        $this->assertFalse($business->is_verified);
        $this->assertDatabaseHas('audit_logs', ['action' => 'business.resubmitted_for_review']);
    }

    public function test_editing_an_approved_business_sends_it_back_for_review(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->put(route('business-owner.businesses.update', $business), ['phone' => '08099999999']);

        $this->assertSame('submitted', $business->refresh()->status);
    }

    public function test_editing_an_expired_business_sends_it_back_for_review(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->verified()->for($owner)->create(['status' => 'expired']);

        $this->actingAs($owner)
            ->put(route('business-owner.businesses.update', $business), ['phone' => '08099999999']);

        $this->assertSame('submitted', $business->refresh()->status);
    }

    public function test_a_resubmitted_business_reappears_in_the_admin_review_queue(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $business = Business::factory()->verified()->for($owner)->create();

        $this->actingAs($owner)
            ->put(route('business-owner.businesses.update', $business), ['phone' => '08099999999']);

        $this->actingAs($admin)
            ->get(route('admin.businesses.index', ['status' => 'submitted']))
            ->assertOk()
            ->assertSeeText($business->business_name);
    }

    public function test_editing_a_draft_business_does_not_change_its_status(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'draft']);

        $this->actingAs($owner)
            ->put(route('business-owner.businesses.update', $business), ['phone' => '08099999999'])
            ->assertSessionHas('success', 'Business updated successfully.');

        $this->assertSame('draft', $business->refresh()->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'business.updated']);
    }

    public function test_forbids_editing_a_rejected_business(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'rejected']);

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.edit', $business))
            ->assertForbidden();
    }

    public function test_owner_can_open_profile_form_for_an_approved_business(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.profile.edit', $business))
            ->assertOk();
    }

    public function test_forbids_profile_form_for_a_business_that_is_not_approved(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'draft']);

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.profile.edit', $business))
            ->assertForbidden();
    }

    public function test_submission_raises_the_configured_certification_fee(): void
    {
        $this->disableDocumentRequirements();

        Setting::set('certification_fee', '25000');
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'draft']);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.submit', $business));

        $this->assertDatabaseHas('fees', [
            'business_id' => $business->id,
            'fee_type' => 'certification',
            'amount' => '25000.00',
            'payment_status' => 'unpaid',
        ]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'fee.issued']);
    }

    public function test_submission_raises_no_fee_when_no_amount_is_configured(): void
    {
        $this->disableDocumentRequirements();

        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'draft']);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.submit', $business));

        $this->assertSame('submitted', $business->refresh()->status);
        $this->assertSame(0, Fee::count());
    }

    private function disableDocumentRequirements(): void
    {
        DocumentType::query()->update(['status' => 'inactive']);
    }
}
