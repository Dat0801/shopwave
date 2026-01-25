<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'image_path' => fake()->imageUrl(1200, 400, 'business'),
            'link' => fake()->url(),
            'description' => fake()->sentence(),
            'is_active' => true,
            'order' => fake()->numberBetween(1, 10),
            'duration' => fake()->numberBetween(3000, 8000),
        ];
    }
}
