<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserManageRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SuperAdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('agency');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->input('status')) {
            $query->where('account_status', $status);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('super-admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('super-admin.users.create');
    }

    public function store(UserManageRequest $request): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $plainPassword = Str::random(12);

        $user = User::create([
            ...$request->validated(),
            'password' => Hash::make($plainPassword),
        ]);

        AuditService::logAction(
            action: 'user.created',
            description: "User '{$user->name}' created by super admin",
            auditable: $user,
        );

        return redirect()->route('super-admin.users.index')
            ->with('success', "User created. Temporary password: {$plainPassword}");
    }

    public function show(User $user): View
    {
        $user->load(['agency', 'businesses', 'auditLogs' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('super-admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('super-admin.users.edit', compact('user'));
    }

    public function update(UserManageRequest $request, User $user): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $validated = $request->validated();

        if ($this->wouldLockOutLastSuperAdmin($user, $validated)) {
            return back()->with('error', 'The last super admin cannot be demoted or suspended.');
        }

        if ($user->is(auth()->user()) && array_key_exists('role', $validated) && $validated['role'] !== $user->role) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $previousRole = $user->role;

        $user->fill(Arr::except($validated, ['role', 'account_status']))
            ->assignRole($validated['role'])
            ->assignAccountStatus($validated['account_status'])
            ->save();

        if ($previousRole !== $user->role) {
            AuditService::logAction(
                action: 'user.role_changed',
                description: "User '{$user->name}' role changed from '{$previousRole}' to '{$user->role}' by super admin",
                auditable: $user,
            );
        }

        AuditService::logAction(
            action: 'user.updated',
            description: "User '{$user->name}' updated by super admin",
            auditable: $user,
        );

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function suspend(User $user): RedirectResponse
    {
        $this->authorize('is-super-admin');

        if ($user->is(auth()->user())) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        if ($this->isLastSuperAdmin($user)) {
            return back()->with('error', 'The last super admin cannot be suspended.');
        }

        $user->update(['account_status' => 'suspended']);

        AuditService::logAction(
            action: 'user.suspended',
            description: "User '{$user->name}' suspended by super admin",
            auditable: $user,
        );

        return back()->with('success', 'User suspended successfully.');
    }

    public function activate(User $user): RedirectResponse
    {
        $this->authorize('is-super-admin');

        $user->update(['account_status' => 'active']);

        AuditService::logAction(
            action: 'user.activated',
            description: "User '{$user->name}' activated by super admin",
            auditable: $user,
        );

        return back()->with('success', 'User activated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('is-super-admin');

        if ($user->is(auth()->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($this->isLastSuperAdmin($user)) {
            return back()->with('error', 'The last super admin cannot be deleted.');
        }

        $userName = $user->name;
        $user->delete();

        AuditService::logAction(
            action: 'user.deleted',
            description: "User '{$userName}' deleted by super admin",
        );

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    private function isLastSuperAdmin(User $user): bool
    {
        return $user->role === 'super_admin'
            && User::where('role', 'super_admin')->count() <= 1;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function wouldLockOutLastSuperAdmin(User $user, array $validated): bool
    {
        if (! $this->isLastSuperAdmin($user)) {
            return false;
        }

        $demoting = array_key_exists('role', $validated) && $validated['role'] !== 'super_admin';
        $suspending = ($validated['account_status'] ?? null) === 'suspended';

        return $demoting || $suspending;
    }
}
