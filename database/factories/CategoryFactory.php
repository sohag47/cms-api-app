<?php

namespace Database\Factories;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected static array $categories = [
        'Electronics', 'Clothing', 'Books', 'Home & Kitchen', 'Beauty',
        'Toys', 'Sports', 'Groceries', 'Furniture', 'Automotive'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(static::$categories);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'status' => fake()->randomElement([
                CategoryStatus::ACTIVE,
                CategoryStatus::ARCHIVED,
                CategoryStatus::INACTIVE,
                CategoryStatus::DISABLED
            ]),
            'parent_id' => null,
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
