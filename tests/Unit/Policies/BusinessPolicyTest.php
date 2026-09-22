<?php

namespace Tests\Unit\Policies;

use App\Models\Business;
use App\Models\User;
use App\Policies\BusinessPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BusinessPolicyTest extends TestCase
{
    /**
     * @return array<string, array{string, bool}>
     */
    public static function ownerUpdateByStatus(): array
    {
        return [
            'draft' => ['draft', true],
            'correction required' => ['correction_required', true],
            'approved' => ['approved', true],
            'verified' => ['verified', true],
            'expired' => ['expired', true],
            'submitted' => ['submitted', false],
            'under review' => ['under_review', false],
            'rejected' => ['rejected', false],
            'suspended' => ['suspended', false],
        ];
    }

    #[DataProvider('ownerUpdateByStatus')]
    public function test_owner_can_update_business_unless_a_decision_is_pending_or_it_is_a_dead_end_status(string $status, bool $allowed): void
    {
        [$owner, $business] = $this->ownerAndBusiness($status);

        $this->assertSame($allowed, (new BusinessPolicy)->update($owner, $business));
    }

    public function test_other_owner_cannot_update_a_draft_business(): void
    {
        [, $business] = $this->ownerAndBusiness('draft');
        $stranger = User::factory()->make(['id' => 999]);

        $this->assertFalse((new BusinessPolicy)->update($stranger, $business));
    }

    public function test_super_admin_can_update_a_business_in_any_status(): void
    {
        [, $business] = $this->ownerAndBusiness('approved');
        $superAdmin = User::factory()->make(['id' => 999, 'role' => 'super_admin']);

        $this->assertTrue((new BusinessPolicy)->update($superAdmin, $business));
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function ownerProfileByStatus(): array
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

    #[DataProvider('ownerProfileByStatus')]
    public function test_owner_can_manage_directory_profile_only_when_business_is_approved(string $status, bool $allowed): void
    {
        [$owner, $business] = $this->ownerAndBusiness($status);

        $this->assertSame($allowed, (new BusinessPolicy)->manageProfile($owner, $business));
    }

    public function test_admin_cannot_manage_a_directory_profile_on_behalf_of_the_owner(): void
    {
        [, $business] = $this->ownerAndBusiness('approved');
        $admin = User::factory()->make(['id' => 999, 'role' => 'admin']);

        $this->assertFalse((new BusinessPolicy)->manageProfile($admin, $business));
    }

    /**
     * @return array{User, Business}
     */
    private function ownerAndBusiness(string $status): array
    {
        $owner = User::factory()->make(['id' => 1]);
        $business = Business::factory()->make([
            'user_id' => $owner->id,
            'sector_id' => null,
            'status' => $status,
        ]);

        return [$owner, $business];
    }
}
