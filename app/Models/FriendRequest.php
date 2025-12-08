<?php

namespace App\Models;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(Closure $param)
 * @method static create(array $array)
 * @method static findOrFail($id)
 */
class FriendRequest extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'status', 'message'];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

}

