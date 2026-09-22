<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin', 'government_official']);
    }

    public function view(User $user, Business $business): bool
    {
        return $business->user_id === $user->id
            || in_array($user->role, ['super_admin', 'admin', 'government_official']);
    }

    public function create(User $user): bool
    {
        return $user->role === 'business_owner';
    }

    /**
     * Owners may only change a business while it is still in their hands;
     * once submitted or approved, changes must go through review.
     */
    public function update(User $user, Business $business): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $business->user_id === $user->id
            && in_array($business->status, ['draft', 'correction_required'], true);
    }

    public function manageProfile(User $user, Business $business): bool
    {
        return $business->user_id === $user->id
            && in_array($business->status, ['approved', 'verified'], true);
    }

    public function delete(User $user, Business $business): bool
    {
        return $user->role === 'super_admin';
    }

    public function review(User $user, Business $business): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    public function approve(User $user, Business $business): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }
}
