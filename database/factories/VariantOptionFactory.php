<?php

namespace Database\Factories;

use App\Models\Variant;
use App\Models\VariantOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VariantOption>
 */
class VariantOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Red', 'Blue', 'Small', 'Large', 'Cotton']),
            'variant_id' => Variant::factory(),
        ];
    }
}
