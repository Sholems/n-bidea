<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'account_status',
        'agency_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'account_status' => 'string',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    public function documentsReviewed(): HasMany
    {
        return $this->hasMany(BusinessDocument::class, 'reviewed_by');
    }

    public function verificationReviews(): HasMany
    {
        return $this->hasMany(VerificationReview::class, 'admin_id');
    }

    public function renewalRequests(): HasMany
    {
        return $this->hasMany(RenewalRequest::class, 'requested_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function scopeRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('account_status', 'active');
    }

    /**
     * Assign the user's role without relying on mass assignment.
     */
    public function assignRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Assign the user's account status without relying on mass assignment.
     */
    public function assignAccountStatus(string $accountStatus): static
    {
        $this->account_status = $accountStatus;

        return $this;
    }

    /**
     * Resolve the dashboard route name for the user's role.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'super_admin' => 'super-admin.dashboard',
            'admin' => 'admin.dashboard',
            'government_official' => 'government.dashboard',
            default => 'business-owner.dashboard',
        };
    }
}
