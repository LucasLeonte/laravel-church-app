<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, true $true)
 * @method static orderBy(string $string)
 * @method static create(array $data)
 * @method static findOrFail($id)
 * @method static orderByRaw(string $string)
 */
class Program extends Model
{
    protected $fillable = [
        'title',
        'description',
        'day_of_week',
        'start_time',
        'end_time',
        'published',
    ];
}
