<?php

namespace App\Models;

use Database\Factories\BusinessProfileFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessProfile extends Model
{
    /** @use HasFactory<BusinessProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
        'logo_path',
        'logo_mime_type',
        'summary',
        'services',
        'operating_locations',
        'trade_interests',
        'certifications',
        'website',
        'contact_preference',
        'status',
        'approved_by',
        'approved_at',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(BusinessInquiry::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('business_profiles.status', 'approved');
    }
}
