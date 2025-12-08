<?php

namespace Database\Factories;

use App\Models\ResourceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResourceCategory>
 */
class ResourceCategoryFactory extends Factory
{
    protected $model = ResourceCategory::class;

    protected static array $categories = [
        'Systematic Theology',
        'Biblical Theology',
        'Apologetics',
        'Church History',
        'Denominations',
        'Pastoral',
        'Sermons',
        'Science',
        'Philosophy',
        'Devotional',
        'Revival',
    ];

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(self::$categories),
            'description' => $this->faker->paragraph(),
        ];
    }
}

