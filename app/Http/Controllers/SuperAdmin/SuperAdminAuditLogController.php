<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuditLogFilterRequest;
use App\Models\AuditLog;
use Illuminate\View\View;

class SuperAdminAuditLogController extends Controller
{
    public function index(AuditLogFilterRequest $request): View
    {
        $this->authorize('is-super-admin');

        $validated = $request->validated();

        $query = AuditLog::with('user');

        if (! empty($validated['user_id'])) {
            $query->where('user_id', $validated['user_id']);
        }

        if (! empty($validated['action'])) {
            $query->where('action', 'like', "%{$validated['action']}%");
        }

        if (! empty($validated['date_from'])) {
            $query->where('created_at', '>=', $validated['date_from']);
        }

        if (! empty($validated['date_to'])) {
            $query->where('created_at', '<=', $validated['date_to'].' 23:59:59');
        }

        $auditLogs = $query->latest()->paginate(50)->withQueryString();

        return view('super-admin.audit-logs.index', compact('auditLogs'));
    }
}
