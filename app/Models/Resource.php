<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static findOrFail($id)
 * @method static orderBy(string $string, string $string1)
 * @property mixed $title
 * @property mixed $content
 * @property mixed|null $author
 * @property CarbonInterface|Carbon|mixed $published_at
 * @property mixed|string $image
 * @property mixed|null $link
 * @property mixed|null $resource_category_id
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
        'link',
        'resource_category_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'resource_category_id');
    }
}
