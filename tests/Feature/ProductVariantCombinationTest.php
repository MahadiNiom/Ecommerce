<?php

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Variant;
use App\Models\VariantOption;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function combinationKey(int ...$optionIds): string
{
    return collect($optionIds)->sort()->values()->implode('-');
}

function combinationFields(int ...$optionIds): array
{
    return [
        'option_ids' => collect($optionIds)->sort()->values()->implode(','),
        'selected' => 0,
        'price' => null,
        'stock' => null,
    ];
}

it('generates product variants for every option combination', function () {
    $product = Product::factory()->create();

    $color = Variant::factory()->create(['name' => 'Color', 'product_id' => $product->id]);
    $red = VariantOption::factory()->create(['name' => 'Red', 'variant_id' => $color->id]);
    $blue = VariantOption::factory()->create(['name' => 'Blue', 'variant_id' => $color->id]);

    $size = Variant::factory()->create(['name' => 'Size', 'product_id' => $product->id]);
    $small = VariantOption::factory()->create(['name' => 'Small', 'variant_id' => $size->id]);
    $large = VariantOption::factory()->create(['name' => 'Large', 'variant_id' => $size->id]);

    $combinations = [
        combinationKey($red->id, $small->id) => [...combinationFields($red->id, $small->id), 'selected' => 1, 'price' => 19.99, 'stock' => 10],
        combinationKey($red->id, $large->id) => [...combinationFields($red->id, $large->id), 'selected' => 1, 'price' => 24.99, 'stock' => 5],
        combinationKey($blue->id, $small->id) => [...combinationFields($blue->id, $small->id), 'selected' => 1, 'price' => 29.99, 'stock' => 8],
        combinationKey($blue->id, $large->id) => [...combinationFields($blue->id, $large->id), 'selected' => 1, 'price' => 34.99, 'stock' => 3],
    ];

    $this->post(route('products.product-variants.assign.store', $product), ['combinations' => $combinations])
        ->assertRedirect(route('products.show', $product));

    $this->assertDatabaseCount('product_variants', 4);
    $this->assertDatabaseCount('product_variant_variant_option', 8);

    $redSmall = ProductVariant::query()
        ->whereHas('variantOptions', fn ($query) => $query->whereKey($red->id))
        ->whereHas('variantOptions', fn ($query) => $query->whereKey($small->id))
        ->first();

    expect($redSmall)->not->toBeNull()
        ->and($redSmall->price)->toBe('19.99')
        ->and($redSmall->stock)->toBe(10);
});

it('updates an existing product variant when its combination is re-assigned', function () {
    $product = Product::factory()->create();

    $color = Variant::factory()->create(['name' => 'Color', 'product_id' => $product->id]);
    $red = VariantOption::factory()->create(['name' => 'Red', 'variant_id' => $color->id]);
    $blue = VariantOption::factory()->create(['name' => 'Blue', 'variant_id' => $color->id]);

    $redKey = combinationKey($red->id);
    $blueKey = combinationKey($blue->id);

    $this->post(route('products.product-variants.assign.store', $product), ['combinations' => [
        $redKey => [...combinationFields($red->id), 'selected' => 1, 'price' => 9.99, 'stock' => 4],
    ]])->assertRedirect();

    $this->assertDatabaseCount('product_variants', 1);

    $this->post(route('products.product-variants.assign.store', $product), ['combinations' => [
        $redKey => [...combinationFields($red->id), 'selected' => 1, 'price' => 15.99, 'stock' => 7],
        $blueKey => [...combinationFields($blue->id), 'selected' => 1, 'price' => 12.50, 'stock' => 2],
    ]])->assertRedirect();

    $this->assertDatabaseCount('product_variants', 2);

    $redVariant = $product->productVariants()->whereHas('variantOptions', fn ($query) => $query->whereKey($red->id))->first();

    expect($redVariant->price)->toBe('15.99')
        ->and($redVariant->stock)->toBe(7);
});

it('deletes a product variant when its combination is deselected', function () {
    $product = Product::factory()->create();

    $color = Variant::factory()->create(['name' => 'Color', 'product_id' => $product->id]);
    $red = VariantOption::factory()->create(['name' => 'Red', 'variant_id' => $color->id]);

    $productVariant = ProductVariant::factory()->create(['product_id' => $product->id]);
    $productVariant->variantOptions()->sync([$red->id]);

    $redKey = combinationKey($red->id);

    $this->post(route('products.product-variants.assign.store', $product), ['combinations' => [
        $redKey => combinationFields($red->id),
    ]])->assertRedirect();

    $this->assertDatabaseCount('product_variants', 0);
    $this->assertDatabaseCount('product_variant_variant_option', 0);
});

it('rejects a combination containing an option that does not belong to the product', function () {
    $product = Product::factory()->create();

    $color = Variant::factory()->create(['name' => 'Color', 'product_id' => $product->id]);
    $red = VariantOption::factory()->create(['name' => 'Red', 'variant_id' => $color->id]);

    $foreign = VariantOption::factory()->create();

    $foreignKey = combinationKey($foreign->id);

    $this->from(route('products.product-variants.assign', $product))
        ->post(route('products.product-variants.assign.store', $product), ['combinations' => [
            $foreignKey => [...combinationFields($red->id, $foreign->id), 'selected' => 1, 'price' => 5, 'stock' => 1],
        ]])
        ->assertSessionHasErrors("combinations.{$foreignKey}.option_ids");
});

it('creates a product variant from a chosen combination', function () {
    $product = Product::factory()->create();

    $color = Variant::factory()->create(['name' => 'Color', 'product_id' => $product->id]);
    $red = VariantOption::factory()->create(['name' => 'Red', 'variant_id' => $color->id]);

    $this->post(route('products.product-variants.store', $product), [
        'options' => [$red->id],
        'price' => 21.50,
        'stock' => 42,
    ])->assertRedirect(route('products.product-variants.index', $product));

    $this->assertDatabaseHas('product_variants', [
        'product_id' => $product->id,
        'price' => '21.50',
        'stock' => 42,
    ]);

    $productVariant = $product->productVariants()->first();

    expect($productVariant->variantOptions()->pluck('variant_options.id'))->toContain($red->id);
});

it('lists every combination on the generation page', function () {
    $product = Product::factory()->create();

    $color = Variant::factory()->create(['name' => 'Color', 'product_id' => $product->id]);
    $size = Variant::factory()->create(['name' => 'Size', 'product_id' => $product->id]);

    $red = VariantOption::factory()->create(['name' => 'Red', 'variant_id' => $color->id]);
    $blue = VariantOption::factory()->create(['name' => 'Blue', 'variant_id' => $color->id]);
    $small = VariantOption::factory()->create(['name' => 'Small', 'variant_id' => $size->id]);
    $large = VariantOption::factory()->create(['name' => 'Large', 'variant_id' => $size->id]);

    $this->get(route('products.product-variants.assign', $product))
        ->assertOk()
        ->assertSee('Color: Red')
        ->assertSee('Color: Blue')
        ->assertSee('name="combinations['.combinationKey($red->id, $small->id).'][price]"', false)
        ->assertSee('name="combinations['.combinationKey($red->id, $large->id).'][price]"', false)
        ->assertSee('name="combinations['.combinationKey($blue->id, $small->id).'][price]"', false)
        ->assertSee('name="combinations['.combinationKey($blue->id, $large->id).'][price]"', false);
});

it('rejects creation when two options come from the same variant', function () {
    $product = Product::factory()->create();

    $color = Variant::factory()->create(['name' => 'Color', 'product_id' => $product->id]);
    $red = VariantOption::factory()->create(['name' => 'Red', 'variant_id' => $color->id]);
    $blue = VariantOption::factory()->create(['name' => 'Blue', 'variant_id' => $color->id]);

    $this->post(route('products.product-variants.store', $product), [
        'options' => [$red->id, $blue->id],
        'price' => 21.50,
        'stock' => 42,
    ])->assertSessionHasErrors('options');

    $this->assertDatabaseCount('product_variants', 0);
});
