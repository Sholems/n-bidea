<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Fee;
use App\Models\RenewalRequest;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FeeWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_renewal_request_raises_the_configured_renewal_fee(): void
    {
        Setting::set('renewal_fee', '15000');
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->post(route('business-owner.renewals.store'), ['business_id' => $business->id])
            ->assertSessionHas('success');

        $this->assertSame(1, RenewalRequest::where('business_id', $business->id)->count());
        $this->assertDatabaseHas('fees', [
            'business_id' => $business->id,
            'fee_type' => 'renewal',
            'amount' => '15000.00',
            'payment_status' => 'unpaid',
        ]);
    }

    public function test_duplicate_renewal_request_does_not_raise_a_second_fee(): void
    {
        Setting::set('renewal_fee', '15000');
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create();

        $this->actingAs($owner)
            ->post(route('business-owner.renewals.store'), ['business_id' => $business->id]);
        $this->actingAs($owner)
            ->post(route('business-owner.renewals.store'), ['business_id' => $business->id])
            ->assertSessionHas('error', 'A pending renewal request already exists for this business.');

        $this->assertSame(1, RenewalRequest::count());
        $this->assertSame(1, Fee::count());
    }

    public function test_rejects_renewal_for_a_business_that_was_never_approved(): void
    {
        Setting::set('renewal_fee', '15000');
        $owner = User::factory()->create();
        $business = Business::factory()->for($owner)->create(['status' => 'draft']);

        $this->actingAs($owner)
            ->post(route('business-owner.renewals.store'), ['business_id' => $business->id])
            ->assertSessionHasErrors(['business_id' => 'Only approved or expired businesses can be renewed.']);

        $this->assertSame(0, RenewalRequest::count());
        $this->assertSame(0, Fee::count());
    }

    public function test_expired_business_can_request_renewal(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->approved()->for($owner)->create(['status' => 'expired']);

        $this->actingAs($owner)
            ->post(route('business-owner.renewals.store'), ['business_id' => $business->id])
            ->assertSessionHas('success');

        $this->assertSame(1, RenewalRequest::where('business_id', $business->id)->count());
    }

    public function test_owner_uploading_proof_marks_fee_pending_confirmation(): void
    {
        Storage::fake('private');
        $owner = User::factory()->create();
        $fee = Fee::factory()->for(Business::factory()->for($owner))->create();

        $this->actingAs($owner)
            ->post(route('business-owner.fees.upload-proof', $fee), [
                'proof_file' => UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf'),
                'payment_reference' => 'REF-778899',
            ])
            ->assertSessionHas('success');

        $fee->refresh();

        $this->assertSame('pending_confirmation', $fee->payment_status);
        $this->assertSame('REF-778899', $fee->payment_reference);
        Storage::disk('private')->assertExists($fee->proof_file_path);
    }

    public function test_reuploading_proof_replaces_the_previous_file(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('payment-proofs/proof.pdf', 'old proof');
        $owner = User::factory()->create();
        $fee = Fee::factory()->pendingConfirmation()->for(Business::factory()->for($owner))->create();

        $this->actingAs($owner)
            ->post(route('business-owner.fees.upload-proof', $fee), [
                'proof_file' => UploadedFile::fake()->create('new-proof.pdf', 100, 'application/pdf'),
            ]);

        $fee->refresh();

        Storage::disk('private')->assertMissing('payment-proofs/proof.pdf');
        Storage::disk('private')->assertExists($fee->proof_file_path);
    }

    public function test_paid_fee_cannot_be_moved_back_to_pending_by_a_new_upload(): void
    {
        Storage::fake('private');
        $owner = User::factory()->create();
        $fee = Fee::factory()->paid()->for(Business::factory()->for($owner))->create();

        $this->actingAs($owner)
            ->post(route('business-owner.fees.upload-proof', $fee), [
                'proof_file' => UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHas('error', 'This fee has already been settled.');

        $fee->refresh();

        $this->assertSame('paid', $fee->payment_status);
        $this->assertSame('payment-proofs/proof.pdf', $fee->proof_file_path);
    }

    public function test_forbids_uploading_proof_for_another_owners_fee(): void
    {
        Storage::fake('private');
        $stranger = User::factory()->create();
        $fee = Fee::factory()->create();

        $this->actingAs($stranger)
            ->post(route('business-owner.fees.upload-proof', $fee), [
                'proof_file' => UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf'),
            ])
            ->assertForbidden();

        $this->assertSame('unpaid', $fee->refresh()->payment_status);
    }

    public function test_owner_sees_raised_fees_on_the_fees_page(): void
    {
        $owner = User::factory()->create();
        Fee::factory()->for(Business::factory()->for($owner)->state(['business_name' => 'Seme Trade Services']))->create([
            'amount' => 25000,
        ]);

        $this->actingAs($owner)
            ->get(route('business-owner.fees.index'))
            ->assertOk()
            ->assertSeeText('Seme Trade Services')
            ->assertSeeText('₦25,000.00');
    }

    public function test_admin_confirming_a_fee_marks_it_paid(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $fee = Fee::factory()->pendingConfirmation()->create();

        $this->actingAs($admin)
            ->post(route('admin.fees.confirm', $fee))
            ->assertSessionHas('success');

        $fee->refresh();

        $this->assertSame('paid', $fee->payment_status);
        $this->assertSame($admin->id, $fee->confirmed_by);
    }
}
