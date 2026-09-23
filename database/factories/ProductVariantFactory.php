<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'price' => fake()->randomFloat(2, 5, 500),
            'stock' => fake()->numberBetween(0, 100),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (ProductVariant $productVariant) {
            $optionIds = VariantOption::query()
                ->whereHas('variant', fn ($query) => $query->whereBelongsTo($productVariant->product))
                ->get()
                ->groupBy('variant_id')
                ->map(fn ($options) => $options->random()->id)
                ->values();

            $productVariant->variantOptions()->attach($optionIds);
        });
    }
}
