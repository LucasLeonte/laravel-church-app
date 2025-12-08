<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, $id)
 * @method static firstOrCreate(array $data)
 */
class FavoriteVerse extends Model
{
    protected $fillable = ['user_id', 'translation', 'book', 'chapter', 'verse'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

