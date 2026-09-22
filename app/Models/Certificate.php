<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'certificate_number',
        'verification_code',
        'status',
        'issued_by',
        'issued_at',
        'expires_at',
        'revoked_at',
        'qr_payload',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function scopeMatchingVerificationQuery(Builder $query, string $value): Builder
    {
        return $query->where(function (Builder $query) use ($value): void {
            $query->where('certificate_number', $value)
                ->orWhere('verification_code', $value)
                ->orWhereHas('business', function (Builder $query) use ($value): void {
                    $query->where('registry_number', $value);
                });
        });
    }

    public function isValid(): bool
    {
        return $this->status === 'active'
            && $this->revoked_at === null
            && $this->expires_at->isFuture()
            && $this->business->is_verified;
    }
}
