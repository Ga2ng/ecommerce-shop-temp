<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $name = fake()->words(3, true);
        $categories = ['T-Shirt', 'Hoodie', 'Cap', 'Bag', 'Mug', 'Sticker', 'Notebook', 'Pen', 'LongSleeve'];
        
        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(3),
            'price' => fake()->randomFloat(2, 50000, 500000),
            'stock' => fake()->numberBetween(0, 100),
            'category' => fake()->randomElement($categories),
            'image' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
