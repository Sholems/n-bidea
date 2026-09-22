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

    public function test_forbids_resubmitting_an_approved_business(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.submit', $business))
            ->assertForbidden();

        $this->assertSame('approved', $business->refresh()->status);
        $this->assertNotNull($business->registry_number);
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

    public function test_forbids_editing_an_approved_business(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.edit', $business))
            ->assertForbidden();
    }

    public function test_owner_can_open_edit_form_for_a_draft_business(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'draft']);

        $this->actingAs($owner)
            ->get(route('business-owner.businesses.edit', $business))
            ->assertOk();
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
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'draft']);

        $this->actingAs($owner)
            ->post(route('business-owner.businesses.submit', $business));

        $this->assertSame('submitted', $business->refresh()->status);
        $this->assertSame(0, Fee::count());
    }
}
