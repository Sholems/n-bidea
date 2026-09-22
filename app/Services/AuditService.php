<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public static function logAction(
        string $action,
        ?string $description = null,
        ?User $user = null,
        ?Model $auditable = null
    ): AuditLog {
        $user = $user ?? Auth::user();
        $request = Request::instance();

        return AuditLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
