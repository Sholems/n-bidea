<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\BusinessDocument;
use App\Models\User;

class BusinessDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'government_official', 'super_admin']);
    }

    public function view(User $user, BusinessDocument $document): bool
    {
        if (in_array($user->role, ['super_admin', 'admin', 'government_official'], true)) {
            return true;
        }

        return $document->business->user_id === $user->id;
    }

    public function create(User $user, Business $business): bool
    {
        return $user->role === 'business_owner' && $business->user_id === $user->id;
    }

    public function update(User $user, BusinessDocument $document): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $document->business->user_id === $user->id
            && in_array($document->status, ['pending', 'rejected', 'expired']);
    }

    public function delete(User $user, BusinessDocument $document): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $document->business->user_id === $user->id && $document->status === 'pending';
    }

    public function review(User $user, BusinessDocument $document): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }
}
