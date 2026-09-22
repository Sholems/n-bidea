<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'staff_number',
        'full_name',
        'job_title',
        'phone',
        'email',
        'nationality',
        'date_of_birth',
        'nin',
        'passport_number',
        'status',
        'correction_response',
        'review_note',
        'reviewed_by',
        'reviewed_at',
        'verified_at',
        'verification_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'reviewed_at' => 'datetime',
            'verified_at' => 'datetime',
            'verification_expires_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StaffDocument::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Staff clearance is only valid while it is approved, unexpired, and the
     * employing business is itself verified.
     */
    public function isValid(): bool
    {
        return $this->status === 'approved'
            && $this->verification_expires_at !== null
            && $this->verification_expires_at->isFuture()
            && $this->business->is_verified;
    }
}
