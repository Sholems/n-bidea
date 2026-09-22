<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }
}
