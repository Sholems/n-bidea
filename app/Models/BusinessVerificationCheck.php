<?php

namespace App\Models;

use Database\Factories\BusinessVerificationCheckFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessVerificationCheck extends Model
{
    /** @use HasFactory<BusinessVerificationCheckFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
        'super_admin_id',
        'method',
        'decision',
        'note',
        'checked_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function superAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'super_admin_id');
    }
}
