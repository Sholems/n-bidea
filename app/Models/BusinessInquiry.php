<?php

namespace App\Models;

use Database\Factories\BusinessInquiryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessInquiry extends Model
{
    /** @use HasFactory<BusinessInquiryFactory> */
    use HasFactory;

    protected $fillable = [
        'business_profile_id',
        'requester_id',
        'requester_name',
        'requester_email',
        'requester_phone',
        'requester_company',
        'interest_type',
        'message',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
