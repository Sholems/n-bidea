<?php

namespace Tests\Unit\Policies;

use App\Models\Business;
use App\Models\StaffMember;
use App\Models\User;
use App\Policies\StaffMemberPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StaffMemberPolicyTest extends TestCase
{
    /**
     * @return array<string, array{string, bool}>
     */
    public static function ownerEditByStatus(): array
    {
        return [
            'draft' => ['draft', true],
            'correction required' => ['correction_required', true],
            'submitted' => ['submitted', false],
            'approved' => ['approved', false],
            'rejected' => ['rejected', false],
            'expired' => ['expired', false],
        ];
    }

    #[DataProvider('ownerEditByStatus')]
    public function test_owner_can_update_and_manage_documents_only_while_draft_or_awaiting_correction(string $status, bool $allowed): void
    {
        [$owner, $staffMember] = $this->ownerAndStaff($status);
        $policy = new StaffMemberPolicy;

        $this->assertSame($allowed, $policy->update($owner, $staffMember));
        $this->assertSame($allowed, $policy->manageDocuments($owner, $staffMember));
    }

    #[DataProvider('ownerEditByStatus')]
    public function test_owner_can_delete_staff_only_while_draft(string $status): void
    {
        [$owner, $staffMember] = $this->ownerAndStaff($status);

        $this->assertSame($status === 'draft', (new StaffMemberPolicy)->delete($owner, $staffMember));
    }

    public function test_other_owner_cannot_view_update_or_delete_staff(): void
    {
        [, $staffMember] = $this->ownerAndStaff('draft');
        $stranger = User::factory()->make(['id' => 999]);
        $policy = new StaffMemberPolicy;

        $this->assertFalse($policy->view($stranger, $staffMember));
        $this->assertFalse($policy->update($stranger, $staffMember));
        $this->assertFalse($policy->delete($stranger, $staffMember));
    }

    /**
     * @return array<string, array{string, bool, bool}>
     */
    public static function roleAccess(): array
    {
        return [
            'super admin' => ['super_admin', true, true],
            'admin' => ['admin', true, true],
            'government official' => ['government_official', true, false],
            'business owner' => ['business_owner', false, false],
        ];
    }

    #[DataProvider('roleAccess')]
    public function test_role_can_view_and_review_staff_records(string $role, bool $canView, bool $canReview): void
    {
        [, $staffMember] = $this->ownerAndStaff('submitted');
        $user = User::factory()->make(['id' => 999, 'role' => $role]);
        $policy = new StaffMemberPolicy;

        $this->assertSame($canView, $policy->view($user, $staffMember));
        $this->assertSame($canView, $policy->viewAny($user));
        $this->assertSame($canReview, $policy->review($user, $staffMember));
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function businessStatusForCreate(): array
    {
        return [
            'approved' => ['approved', true],
            'verified' => ['verified', true],
            'draft' => ['draft', false],
            'submitted' => ['submitted', false],
            'rejected' => ['rejected', false],
            'expired' => ['expired', false],
        ];
    }

    #[DataProvider('businessStatusForCreate')]
    public function test_owner_can_add_staff_only_to_an_approved_business(string $status, bool $allowed): void
    {
        $owner = User::factory()->make(['id' => 1]);
        $business = Business::factory()->make(['user_id' => 1, 'sector_id' => null, 'status' => $status]);

        $this->assertSame($allowed, (new StaffMemberPolicy)->create($owner, $business));
    }

    public function test_other_owner_cannot_add_staff_to_an_approved_business(): void
    {
        $stranger = User::factory()->make(['id' => 999]);
        $business = Business::factory()->make(['user_id' => 1, 'sector_id' => null, 'status' => 'approved']);

        $this->assertFalse((new StaffMemberPolicy)->create($stranger, $business));
    }

    /**
     * @return array{User, StaffMember}
     */
    private function ownerAndStaff(string $status): array
    {
        $owner = User::factory()->make(['id' => 1]);
        $business = Business::factory()->make(['user_id' => 1, 'sector_id' => null, 'status' => 'approved']);
        $staffMember = StaffMember::factory()->make(['business_id' => null, 'status' => $status]);
        $staffMember->setRelation('business', $business);

        return [$owner, $staffMember];
    }
}
