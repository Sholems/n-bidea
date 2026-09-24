<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\BusinessDocument;
use App\Models\BusinessProfile;
use App\Models\Fee;
use App\Models\RenewalRequest;
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
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureDashboardNavigation();

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

    private function configureDashboardNavigation(): void
    {
        View::composer('layouts.dashboard', function (ViewInstance $view): void {
            $user = auth()->user();
            $badges = [];

            if ($user?->role === 'business_owner') {
                $businessIds = $user->businesses()->pluck('id');
                $badges = [
                    'businesses' => Business::whereIn('id', $businessIds)->where('status', 'correction_required')->count(),
                    'fees' => Fee::whereIn('business_id', $businessIds)->where('payment_status', 'unpaid')->count(),
                ];
            }

            if ($user?->role === 'admin') {
                $badges = $this->administrativeNavigationBadges();
            }

            if ($user?->role === 'super_admin') {
                $badges = $this->administrativeNavigationBadges() + [
                    'verification' => Business::where('status', 'approved')->count(),
                    'users' => User::where('account_status', 'pending')->count(),
                ];
            }

            $view->with('navigationBadges', $badges);
        });
    }

    /**
     * @return array<string, int>
     */
    private function administrativeNavigationBadges(): array
    {
        return [
            'applications' => Business::whereIn('status', ['submitted', 'under_review'])->count(),
            'profiles' => BusinessProfile::where('status', 'pending')->count(),
            'documents' => BusinessDocument::where('status', 'pending')->count(),
            'staff' => StaffMember::where('status', 'submitted')->count(),
            'renewals' => RenewalRequest::where('status', 'pending')->count(),
            'fees' => Fee::where('payment_status', 'pending_confirmation')->count(),
        ];
    }
}
