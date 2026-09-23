<?php

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Variant;
use App\Models\VariantOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function shopProductWithVariant(): array
{
    $product = Product::factory()->create(['name' => 'Classic T-Shirt']);
    $size = Variant::factory()->create(['name' => 'Size', 'product_id' => $product->id]);
    $large = VariantOption::factory()->create(['name' => 'Large', 'variant_id' => $size->id]);

    $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 19.99, 'stock' => 5]);
    $variant->variantOptions()->sync([$large->id]);

    return [$product, $variant];
}
