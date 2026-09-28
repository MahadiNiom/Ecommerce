<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function searchProduct(string $name, array $attributes = []): Product
{
    $product = Product::factory()->create(array_merge(['name' => $name], $attributes));

    ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 19.99, 'stock' => 5]);

    return $product;
}

it('returns matching products with their storefront price and link', function () {
    $product = searchProduct('Canvas Tote Bag');

    $this->getJson(route('search', ['q' => 'tote']))
        ->assertOk()
        ->assertJsonPath('query', 'tote')
        ->assertJsonPath('tooShort', false)
        ->assertJsonPath('resultsUrl', route('shop.index', ['q' => 'tote']))
        ->assertJsonCount(1, 'products')
        ->assertJsonPath('products.0.id', $product->id)
        ->assertJsonPath('products.0.name', 'Canvas Tote Bag')
        ->assertJsonPath('products.0.url', route('shop.show', $product))
        ->assertJsonPath('products.0.price', 'From $19.99');
});

it('matches products on their category and brand name', function () {
    $category = Category::create(['name' => 'Footwear']);
    $brand = Brand::create(['name' => 'Acme Trail']);

    $byCategory = searchProduct('River Runner', ['category_id' => $category->id]);
    $byBrand = searchProduct('Day Pack', ['brand_id' => $brand->id]);
    searchProduct('Wool Socks');

    $this->getJson(route('search', ['q' => 'footwear']))
        ->assertOk()
        ->assertJsonCount(1, 'products')
        ->assertJsonPath('products.0.id', $byCategory->id)
        ->assertJsonPath('products.0.category', 'Footwear');

    $this->getJson(route('search', ['q' => 'acme trail']))
        ->assertOk()
        ->assertJsonCount(1, 'products')
        ->assertJsonPath('products.0.id', $byBrand->id)
        ->assertJsonPath('products.0.brand', 'Acme Trail');
});

it('suggests matching categories, brands, and tags linked to their shop filter', function () {
    $category = Category::create(['name' => 'Linen Shirts']);
    $brand = Brand::create(['name' => 'Linen Co']);
    $tag = Tag::create(['name' => 'linen-blend']);
    Category::create(['name' => 'Denim Jackets']);
    Brand::create(['name' => 'Northline']);
    Tag::create(['name' => 'denim-blend']);

    $this->getJson(route('search', ['q' => 'linen']))
        ->assertOk()
        ->assertJsonCount(1, 'categories')
        ->assertJsonPath('categories.0.type', 'Category')
        ->assertJsonPath('categories.0.label', 'Linen Shirts')
        ->assertJsonPath('categories.0.meta', '0 products')
        ->assertJsonPath('categories.0.url', route('shop.index', ['category' => $category->id]))
        ->assertJsonCount(1, 'brands')
        ->assertJsonPath('brands.0.label', 'Linen Co')
        ->assertJsonPath('brands.0.url', route('shop.index', ['brand' => $brand->id]))
        ->assertJsonCount(1, 'tags')
        ->assertJsonPath('tags.0.label', 'linen-blend')
        ->assertJsonPath('tags.0.url', route('shop.index', ['tag' => $tag->id]));
});

it('reports how many products a suggestion covers', function () {
    $category = Category::create(['name' => 'Clothing']);
    searchProduct('Tee', ['category_id' => $category->id]);
    searchProduct('Hoodie', ['category_id' => $category->id]);

    $this->getJson(route('search', ['q' => 'clothing']))
        ->assertOk()
        ->assertJsonPath('categories.0.meta', '2 products');
});

it('asks for more characters instead of searching a single character term', function () {
    $this->getJson(route('search', ['q' => 'a']))
        ->assertOk()
        ->assertJsonPath('tooShort', true)
        ->assertJsonPath('products', [])
        ->assertJsonPath('categories', [])
        ->assertJsonPath('brands', [])
        ->assertJsonPath('tags', []);

    $this->getJson(route('search'))
        ->assertOk()
        ->assertJsonPath('tooShort', true);
});

it('treats wildcard characters in the term as literal text', function () {
    searchProduct('Classic T-Shirt');
    searchProduct('50% Off Hoodie');

    $this->getJson(route('search', ['q' => '50%']))
        ->assertOk()
        ->assertJsonCount(1, 'products')
        ->assertJsonPath('products.0.name', '50% Off Hoodie');

    $this->getJson(route('search', ['q' => 'T_Shirt']))
        ->assertOk()
        ->assertJsonCount(0, 'products');

    $this->getJson(route('search', ['q' => '%_']))
        ->assertOk()
        ->assertJsonCount(0, 'products');
});

it('rejects a search term longer than 100 characters', function () {
    $this->getJson(route('search', ['q' => str_repeat('a', 101)]))
        ->assertStatus(422)
        ->assertJsonValidationErrors('q');
});

it('filters the shop index by the search term', function () {
    searchProduct('Canvas Tote Bag');
    searchProduct('Wool Socks');

    $this->get(route('shop.index', ['q' => 'tote']))
        ->assertOk()
        ->assertSee('Canvas Tote Bag')
        ->assertDontSee('Wool Socks');
});

it('combines the search term with the category, brand, and tag filters', function () {
    $category = Category::create(['name' => 'Clothing']);
    $otherCategory = Category::create(['name' => 'Footwear']);
    $brand = Brand::create(['name' => 'Acme']);
    $tag = Tag::create(['name' => 'trending']);

    $match = searchProduct('Canvas Tote Bag', ['category_id' => $category->id, 'brand_id' => $brand->id]);
    $match->tags()->attach($tag->id);

    $wrongCategory = searchProduct('Canvas Tote Pouch', ['category_id' => $otherCategory->id, 'brand_id' => $brand->id]);
    $wrongCategory->tags()->attach($tag->id);

    $this->get(route('shop.index', [
        'q' => 'canvas',
        'category' => $category->id,
        'brand' => $brand->id,
        'tag' => $tag->id,
    ]))->assertOk()
        ->assertSee('Canvas Tote Bag')
        ->assertDontSee('Canvas Tote Pouch');
});

it('lists the whole catalogue when the shop search term is blank', function () {
    searchProduct('Canvas Tote Bag');
    searchProduct('Wool Socks');

    $this->get(route('shop.index', ['q' => '   ']))
        ->assertOk()
        ->assertSee('Canvas Tote Bag')
        ->assertSee('Wool Socks');
});

it('reports the active search term on the shop page', function () {
    searchProduct('Canvas Tote Bag');
    searchProduct('Wool Socks');

    $this->get(route('shop.index', ['q' => 'tote']))
        ->assertOk()
        ->assertSee('1 match for', escape: false)
        ->assertSee('value="tote"', escape: false);
});
