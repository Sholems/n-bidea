<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenewalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'requested_by',
        'status',
        'admin_id',
        'admin_note',
        'reason',
        'previous_expiry_date',
        'new_expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'previous_expiry_date' => 'datetime',
            'new_expiry_date' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
