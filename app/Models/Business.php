<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Business extends Model
{
    use HasFactory;

    public const COUNTRIES = [
        'NG' => 'Nigeria',
        'BJ' => 'Benin',
    ];

    protected $fillable = [
        'user_id',
        'registry_number',
        'business_name',
        'trading_name',
        'registration_number',
        'cac_number',
        'nrs_number',
        'nin',
        'business_type',
        'country_code',
        'sector_id',
        'description',
        'correction_response',
        'address',
        'state',
        'lga',
        'city',
        'phone',
        'email',
        'website',
        'contact_person_name',
        'contact_person_phone',
        'contact_person_email',
        'trade_activity',
        'border_route',
        'status',
        'verified_at',
        'verification_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'verification_expires_at' => 'datetime',
            'status' => 'string',
        ];
    }

    protected function isVerified(): Attribute
    {
        return Attribute::get(function () {
            return $this->status === 'verified'
                && $this->verification_expires_at
                && $this->verification_expires_at->isFuture();
        });
    }

    protected function isExpired(): Attribute
    {
        return Attribute::get(function () {
            return $this->verification_expires_at
                && $this->verification_expires_at->isPast();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BusinessDocument::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(BusinessProfile::class);
    }

    public function verificationReviews(): HasMany
    {
        return $this->hasMany(VerificationReview::class);
    }

    public function verificationChecks(): HasMany
    {
        return $this->hasMany(BusinessVerificationCheck::class);
    }

    public function renewalRequests(): HasMany
    {
        return $this->hasMany(RenewalRequest::class);
    }

    public function staffMembers(): HasMany
    {
        return $this->hasMany(StaffMember::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }
}
