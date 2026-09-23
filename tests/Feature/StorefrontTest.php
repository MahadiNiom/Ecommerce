<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tag;
use App\Models\User;
use App\Models\Variant;
use App\Models\VariantOption;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function storefrontVariant(Product $product): ProductVariant
{
    $size = Variant::factory()->create(['name' => 'Size', 'product_id' => $product->id]);
    $large = VariantOption::factory()->create(['name' => 'Large', 'variant_id' => $size->id]);

    $variant = ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 19.99, 'stock' => 5]);
    $variant->variantOptions()->sync([$large->id]);

    return $variant;
}

it('lists products on the shop index', function () {
    $product = Product::factory()->create(['name' => 'Classic T-Shirt']);
    storefrontVariant($product);

    $this->get(route('shop.index'))
        ->assertOk()
        ->assertSee('Classic T-Shirt')
        ->assertSee('From $19.99');
});

it('filters the shop index by category, brand, and tag', function () {
    $category = Category::create(['name' => 'Clothing']);
    $otherCategory = Category::create(['name' => 'Footwear']);
    $brand = Brand::create(['name' => 'Acme']);
    $tag = Tag::create(['name' => 'trending']);

    $matched = Product::factory()->create(['name' => 'Matched', 'category_id' => $category->id, 'brand_id' => $brand->id]);
    $matched->tags()->attach($tag->id);
    storefrontVariant($matched);

    $other = Product::factory()->create(['name' => 'Other', 'category_id' => $otherCategory->id]);
    storefrontVariant($other);

    $this->get(route('shop.index', [
        'category' => $category->id,
        'brand' => $brand->id,
        'tag' => $tag->id,
    ]))->assertOk()
        ->assertSee('Matched')
        ->assertDontSee('Other');
});

it('shows product details on the shop page for a guest', function () {
    $product = Product::factory()->create(['name' => 'Classic T-Shirt']);
    $variant = storefrontVariant($product);

    $this->get(route('shop.show', $product))
        ->assertOk()
        ->assertSee('Classic T-Shirt')
        ->assertSee('Size: Large')
        ->assertSee('Log in to buy')
        ->assertDontSee('Add to cart');
});

it('shows add to cart controls on the shop page for an authenticated user', function () {
    $variant = storefrontVariant(Product::factory()->create(['name' => 'Classic T-Shirt']));
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('shop.show', $variant->product))
        ->assertOk()
        ->assertSee('Add to cart')
        ->assertDontSee('Log in to buy');
});

it('shows the price of a variant-less product on the shop index', function () {
    Product::factory()->create(['name' => 'Canvas Tote Bag', 'price' => 24.99, 'stock' => 8]);

    $this->get(route('shop.index'))
        ->assertOk()
        ->assertSee('Canvas Tote Bag')
        ->assertSee('$24.99');
});

it('shows price and buy controls for a variant-less product to a guest', function () {
    $product = Product::factory()->create(['name' => 'Canvas Tote Bag', 'price' => 24.99, 'stock' => 8]);

    $this->get(route('shop.show', $product))
        ->assertOk()
        ->assertSee('Canvas Tote Bag')
        ->assertSee('$24.99')
        ->assertSee('Log in to buy')
        ->assertDontSee('Add to cart');
});

it('shows add to cart controls for a variant-less product to an authenticated user', function () {
    $product = Product::factory()->create(['name' => 'Canvas Tote Bag', 'price' => 24.99, 'stock' => 8]);
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('shop.show', $product))
        ->assertOk()
        ->assertSee('Add to cart')
        ->assertDontSee('Log in to buy');
});

it('marks a variant-less product without a price as unavailable', function () {
    $product = Product::factory()->create(['name' => 'Coming Soon']);

    $this->get(route('shop.show', $product))
        ->assertOk()
        ->assertSee('not available for purchase yet')
        ->assertDontSee('Add to Cart');
});
