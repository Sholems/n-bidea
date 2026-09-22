<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\StaffMember;
use App\Models\User;

class StaffMemberPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin', 'government_official'], true);
    }

    public function view(User $user, StaffMember $staffMember): bool
    {
        return in_array($user->role, ['super_admin', 'admin', 'government_official'], true)
            || $staffMember->business->user_id === $user->id;
    }

    /**
     * Staff can only be added to a business that has itself been approved.
     */
    public function create(User $user, Business $business): bool
    {
        return $business->user_id === $user->id
            && in_array($business->status, ['approved', 'verified'], true);
    }

    public function update(User $user, StaffMember $staffMember): bool
    {
        return $this->ownsAndCanEdit($user, $staffMember);
    }

    public function delete(User $user, StaffMember $staffMember): bool
    {
        return $staffMember->business->user_id === $user->id
            && $staffMember->status === 'draft';
    }

    public function manageDocuments(User $user, StaffMember $staffMember): bool
    {
        return $this->ownsAndCanEdit($user, $staffMember);
    }

    public function review(User $user, StaffMember $staffMember): bool
    {
        return in_array($user->role, ['super_admin', 'admin'], true);
    }

    private function ownsAndCanEdit(User $user, StaffMember $staffMember): bool
    {
        return $staffMember->business->user_id === $user->id
            && in_array($staffMember->status, ['draft', 'correction_required'], true);
    }
}
