<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->randomFloat(2, 10, 1000);
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'price' => $price,
            'sale_price' => fake()->boolean(20) ? $price * 0.8 : null,
            'stock' => fake()->numberBetween(0, 100),
            'description' => fake()->paragraph(),
            'category_id' => Category::factory(),
            'status' => true,
            'image_path' => fake()->imageUrl(640, 480, 'product'),
        ];
    }
}
