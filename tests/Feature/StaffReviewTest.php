<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\StaffDocument;
use App\Models\StaffMember;
use App\Models\User;
use App\Notifications\StaffReviewDecisionNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_approval_issues_a_staff_number_and_a_one_year_expiry(): void
    {
        Notification::fake();
        $admin = $this->admin();
        $staffMember = StaffMember::factory()->submitted()->create();

        $this->actingAs($admin)
            ->post(route('admin.staff.review', $staffMember), ['decision' => 'approved', 'note' => 'All documents check out.'])
            ->assertSessionHas('success');

        $staffMember->refresh();

        $this->assertSame('approved', $staffMember->status);
        $this->assertMatchesRegularExpression('/^NBCCI-STF-'.now()->format('Y').'-000001$/', $staffMember->staff_number);
        $this->assertTrue($staffMember->verification_expires_at->isFuture());
        $this->assertTrue($staffMember->verification_expires_at->between(now()->addDays(364), now()->addDays(366)));
        $this->assertSame($admin->id, $staffMember->reviewed_by);
        $this->assertSame('All documents check out.', $staffMember->review_note);
        $this->assertDatabaseHas('audit_logs', ['action' => 'staff.reviewed.approved']);
    }

    public function test_staff_numbers_increment_across_approvals(): void
    {
        Notification::fake();
        $admin = $this->admin();
        $first = StaffMember::factory()->submitted()->create();
        $second = StaffMember::factory()->submitted()->create();

        $this->actingAs($admin)->post(route('admin.staff.review', $first), ['decision' => 'approved']);
        $this->actingAs($admin)->post(route('admin.staff.review', $second), ['decision' => 'approved']);

        $this->assertStringEndsWith('000001', $first->refresh()->staff_number);
        $this->assertStringEndsWith('000002', $second->refresh()->staff_number);
    }

    public function test_approval_notifies_the_business_owner(): void
    {
        Notification::fake();
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->submitted()->for(Business::factory()->approved()->for($owner))->create();

        $this->actingAs($this->admin())
            ->post(route('admin.staff.review', $staffMember), ['decision' => 'approved']);

        Notification::assertSentTo($owner, StaffReviewDecisionNotification::class, function ($notification) use ($staffMember) {
            return $notification->decision === 'approved' && $notification->staffMember->is($staffMember);
        });
    }

    public function test_correction_request_returns_the_staff_member_without_a_staff_number(): void
    {
        Notification::fake();
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->submitted()->for(Business::factory()->approved()->for($owner))->create();

        $this->actingAs($this->admin())
            ->post(route('admin.staff.review', $staffMember), ['decision' => 'correction_required', 'note' => 'Photo is blurry.']);

        $staffMember->refresh();

        $this->assertSame('correction_required', $staffMember->status);
        $this->assertSame('Photo is blurry.', $staffMember->review_note);
        $this->assertNull($staffMember->staff_number);
        $this->assertNull($staffMember->verification_expires_at);
        Notification::assertSentTo($owner, StaffReviewDecisionNotification::class, fn ($n) => $n->decision === 'correction_required');
    }

    public function test_rejection_is_recorded_and_final(): void
    {
        Notification::fake();
        $admin = $this->admin();
        $staffMember = StaffMember::factory()->submitted()->create();

        $this->actingAs($admin)
            ->post(route('admin.staff.review', $staffMember), ['decision' => 'rejected', 'note' => 'Invalid identity.']);

        $this->assertSame('rejected', $staffMember->refresh()->status);

        $this->actingAs($admin)
            ->post(route('admin.staff.review', $staffMember), ['decision' => 'approved'])
            ->assertStatus(422);

        $this->assertSame('rejected', $staffMember->refresh()->status);
    }

    public function test_rejects_reviewing_a_staff_member_that_is_not_submitted(): void
    {
        Notification::fake();
        $staffMember = StaffMember::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.staff.review', $staffMember), ['decision' => 'approved'])
            ->assertStatus(422);

        $this->assertSame('draft', $staffMember->refresh()->status);
        Notification::assertNothingSent();
    }

    public function test_rejects_approving_staff_of_a_business_that_is_no_longer_approved(): void
    {
        Notification::fake();
        $business = Business::factory()->approved()->create(['status' => 'expired']);
        $staffMember = StaffMember::factory()->submitted()->for($business)->create();

        $this->actingAs($this->admin())
            ->post(route('admin.staff.review', $staffMember), ['decision' => 'approved'])
            ->assertStatus(422);

        $this->assertSame('submitted', $staffMember->refresh()->status);
    }

    public function test_rejects_an_unknown_decision(): void
    {
        $staffMember = StaffMember::factory()->submitted()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.staff.review', $staffMember), ['decision' => 'maybe'])
            ->assertInvalid(['decision']);

        $this->assertSame('submitted', $staffMember->refresh()->status);
    }

    public function test_forbids_owners_and_officials_from_reviewing_staff(): void
    {
        $staffMember = StaffMember::factory()->submitted()->create();

        foreach (['business_owner', 'government_official'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->post(route('admin.staff.review', $staffMember), ['decision' => 'approved'])
                ->assertForbidden();
        }

        $this->assertSame('submitted', $staffMember->refresh()->status);
    }

    public function test_guest_is_redirected_to_login_when_reviewing(): void
    {
        $staffMember = StaffMember::factory()->submitted()->create();

        $this->post(route('admin.staff.review', $staffMember), ['decision' => 'approved'])
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_open_the_staff_list_and_detail_pages(): void
    {
        $staffMember = StaffMember::factory()->submitted()->create(['full_name' => 'Chidi Okafor']);
        StaffMember::factory()->create(['full_name' => 'Draft Person']);

        $this->actingAs($this->admin())
            ->get(route('admin.staff.index', ['status' => 'submitted']))
            ->assertOk()
            ->assertSeeText('Chidi Okafor')
            ->assertDontSeeText('Draft Person');

        $this->actingAs($this->admin())
            ->get(route('admin.staff.show', $staffMember))
            ->assertOk()
            ->assertSeeText('Chidi Okafor')
            ->assertSeeText('Review Decision');
    }

    public function test_staff_list_can_be_searched_by_business_name(): void
    {
        StaffMember::factory()->submitted()->for(Business::factory()->approved()->state(['business_name' => 'Seme Freight Ltd']))->create(['full_name' => 'Found Person']);
        StaffMember::factory()->submitted()->create(['full_name' => 'Other Person']);

        $this->actingAs($this->admin())
            ->get(route('admin.staff.index', ['search' => 'Seme Freight']))
            ->assertOk()
            ->assertSeeText('Found Person')
            ->assertDontSeeText('Other Person');
    }

    public function test_forbids_owners_from_the_admin_staff_pages(): void
    {
        $staffMember = StaffMember::factory()->submitted()->create();
        $owner = User::factory()->create();

        $this->actingAs($owner)->get(route('admin.staff.index'))->assertForbidden();
        $this->actingAs($owner)->get(route('admin.staff.show', $staffMember))->assertForbidden();
    }

    public function test_admin_accepts_and_rejects_individual_documents(): void
    {
        $admin = $this->admin();
        $document = StaffDocument::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.staff-documents.review', $document), ['status' => 'rejected', 'note' => 'Expired passport.'])
            ->assertSessionHas('success');

        $document->refresh();

        $this->assertSame('rejected', $document->status);
        $this->assertSame('Expired passport.', $document->admin_note);
        $this->assertSame($admin->id, $document->reviewed_by);

        $this->actingAs($admin)
            ->post(route('admin.staff-documents.review', $document), ['status' => 'accepted']);

        $this->assertSame('accepted', $document->refresh()->status);
    }

    public function test_rejects_repeating_the_same_document_decision(): void
    {
        $document = StaffDocument::factory()->accepted()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.staff-documents.review', $document), ['status' => 'accepted'])
            ->assertStatus(422);
    }

    public function test_rejects_an_invalid_document_decision(): void
    {
        $document = StaffDocument::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.staff-documents.review', $document), ['status' => 'pending'])
            ->assertInvalid(['status']);
    }

    public function test_forbids_owners_from_reviewing_their_own_staff_documents(): void
    {
        $owner = User::factory()->create();
        $staffMember = StaffMember::factory()->for(Business::factory()->approved()->for($owner))->create();
        $document = StaffDocument::factory()->for($staffMember)->create();

        $this->actingAs($owner)
            ->post(route('admin.staff-documents.review', $document), ['status' => 'accepted'])
            ->assertForbidden();

        $this->assertSame('pending', $document->refresh()->status);
    }

    public function test_admin_downloads_a_staff_document(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('staff-documents/1/doc.pdf', 'content');
        $document = StaffDocument::factory()->create([
            'file_path' => 'staff-documents/1/doc.pdf',
            'original_file_name' => 'nin-slip.pdf',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.staff-documents.download', $document))
            ->assertDownload('nin-slip.pdf');
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }
}
