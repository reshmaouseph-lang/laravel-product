<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'product_name' => $this->faker->words(2, true),
            'product_price' => $this->faker->numberBetween(100, 1000),
            'product_description' => $this->faker->sentence(),
            'product_images' => [],
        ];
    }
}
