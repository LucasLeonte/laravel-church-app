<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @method static findOrFail($id)
 * @property mixed $title
 * @property mixed $content
 * @property mixed|null $author
 * @property CarbonInterface|Carbon|mixed $published_at
 * @property mixed|string $image
 */
class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'content',
        'published_at',
        'author',
    ];
}

