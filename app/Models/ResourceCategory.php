<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static findOrFail($id)
 * @method static orderBy(string $string)
 * @method static create(array $data)
 */
class ResourceCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }
}

