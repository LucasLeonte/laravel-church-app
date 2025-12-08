<?php

namespace App\Models;

use Closure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static firstOrCreate(string[] $array)
 * @method static create(array $data)
 * @method static findOrFail($id)
 * @method static orderBy(string $string)
 * @method static whereHas(string $string, Closure $param)
 */
class FaqCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public static function where(string $string, string $string1, string $qStr)
    {
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }
}
