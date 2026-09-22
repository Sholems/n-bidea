<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\BusinessDocument;
use App\Models\StaffMember;
use App\Models\User;
use App\Policies\AuditLogPolicy;
use App\Policies\BusinessDocumentPolicy;
use App\Policies\BusinessPolicy;
use App\Policies\StaffMemberPolicy;
use App\Policies\UserPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureRateLimiting();

        Gate::policy(Business::class, BusinessPolicy::class);
        Gate::policy(BusinessDocument::class, BusinessDocumentPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
        Gate::policy(StaffMember::class, StaffMemberPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        Gate::define('is-super-admin', fn (User $user) => $user->role === 'super_admin');
        Gate::define('is-admin', fn (User $user) => $user->role === 'admin');
        Gate::define('is-business-owner', fn (User $user) => $user->role === 'business_owner');
        Gate::define('is-government-official', fn (User $user) => $user->role === 'government_official');
        Gate::define('is-admin-or-super', fn (User $user) => in_array($user->role, ['super_admin', 'admin']));
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('auth-login', fn (Request $request) => Limit::perMinute(5)
            ->by(Str::lower((string) $request->input('email')).'|'.$request->ip()));

        RateLimiter::for('auth-register', fn (Request $request) => Limit::perMinute(3)
            ->by($request->ip()));

        RateLimiter::for('auth-password', fn (Request $request) => Limit::perMinute(5)
            ->by(Str::lower((string) $request->input('email')).'|'.$request->ip()));

        RateLimiter::for('inquiries', fn (Request $request) => Limit::perMinute(10)
            ->by($request->user()?->id ?: $request->ip()));
    }
}
