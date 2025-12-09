<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\FavoriteVerse;
use App\Models\FriendRequest;

/**
 * @method static firstOrCreate(string[] $array, array $array1)
 * @method static orderBy(string $string)
 * @method static create(array $array)
 * @method static where(string $string, true $true)
 * @method static firstWhere(string $string, string $string1)
 * @property mixed $id
 * @property mixed $is_admin
 */
class User extends Authenticatable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'is_admin',
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'birthdate',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * User has many favorite verses.
     */
    public function favoriteVerses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FavoriteVerse::class);
    }

    /**
     * Friend requests sent by this user.
     */
    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'sender_id');
    }

    /**
     * Friend requests received by this user.
     */
    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(FriendRequest::class, 'receiver_id');
    }

    /**
     * Convenience: check if this user is friends with another user (accepted status exists in either direction).
     */
    public function isFriendWith(int $otherUserId): bool
    {
        return FriendRequest::where(function ($q) use ($otherUserId) {
            $q->where('sender_id', $this->id)->where('receiver_id', $otherUserId);
        })->orWhere(function ($q) use ($otherUserId) {
            $q->where('sender_id', $otherUserId)->where('receiver_id', $this->id);
        })->where('status', 'accepted')->exists();
    }

    /**
     * Return the friend request status relative to another user.
     * Possible return values: 'sent' (I sent a pending), 'received' (they sent me a pending),
     * 'accepted', 'declined', 'cancelled', or null if no request exists.
     */
    public function friendRequestStatusWith(int $otherUserId): ?string
    {
        $req = FriendRequest::where(function ($q) use ($otherUserId) {
            $q->where('sender_id', $this->id)->where('receiver_id', $otherUserId);
        })->orWhere(function ($q) use ($otherUserId) {
            $q->where('sender_id', $otherUserId)->where('receiver_id', $this->id);
        })->first();

        if (! $req) {
            return null;
        }

        if ($req->status === 'accepted') {
            return 'accepted';
        }

        if ($req->status === 'pending') {
            if ($req->sender_id === $this->id) {
                return 'sent';
            }

            return 'received';
        }

        return $req->status; // declined, cancelled, etc.
    }
}
