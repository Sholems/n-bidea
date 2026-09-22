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
     * Owners may edit their business except while a decision is actively
     * pending (submitted/under_review, so the reviewer isn't looking at a
     * moving target) or the business is in a dead-end state with no defined
     * recovery path (rejected, suspended).
     */
    public function update(User $user, Business $business): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $business->user_id === $user->id
            && in_array($business->status, ['draft', 'correction_required', 'approved', 'verified', 'expired'], true);
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

    public function verify(User $user, Business $business): bool
    {
        return $user->role === 'super_admin' && $business->status === 'approved';
    }
}
