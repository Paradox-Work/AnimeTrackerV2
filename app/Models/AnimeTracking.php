<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimeTracking extends Model
{
    protected $fillable = [
        'user_id',
        'anime_id',
        'watched_episodes',
        'status',
        'score',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
