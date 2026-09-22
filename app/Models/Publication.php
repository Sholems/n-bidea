<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_category_id',
        'uploaded_by',
        'title',
        'slug',
        'description',
        'file_path',
        'original_file_name',
        'mime_type',
        'file_size',
        'audience',
        'status',
        'published_at',
        'download_count',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'download_count' => 'integer',
            'file_size' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ContentCategory::class, 'content_category_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('audience', 'public')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
