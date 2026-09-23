<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests away from the wishlist', function () {
    $this->get(route('wishlist.index'))->assertRedirect(route('login'));
});

it('shows an empty wishlist to an authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('wishlist.index'))
        ->assertOk()
        ->assertSee('Your wishlist is empty');
});

it('adds a variant to the wishlist', function () {
    [, $variant] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('wishlist.store'), ['product_variant_id' => $variant->id])->assertRedirect();

    expect(auth()->user()->wishlistItems()->first()->product_variant_id)->toBe($variant->id);
});

it('does not duplicate a variant already on the wishlist', function () {
    [, $variant] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('wishlist.store'), ['product_variant_id' => $variant->id])->assertRedirect();
    $this->post(route('wishlist.store'), ['product_variant_id' => $variant->id])->assertRedirect();

    $this->assertDatabaseCount('wishlist_items', 1);
});

it('removes an item from the wishlist', function () {
    [, $variant] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->post(route('wishlist.store'), ['product_variant_id' => $variant->id])->assertRedirect();

    $wishlistItem = auth()->user()->wishlistItems()->first();

    $this->delete(route('wishlist.destroy', $wishlistItem))
        ->assertRedirect(route('wishlist.index'));

    $this->assertDatabaseMissing('wishlist_items', ['id' => $wishlistItem->id]);
});

it('rejects removing an item from another user\'s wishlist', function () {
    [, $variant] = shopProductWithVariant();
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $this->post(route('wishlist.store'), ['product_variant_id' => $variant->id])->assertRedirect();
    $wishlistItem = $owner->wishlistItems()->first();

    $this->actingAs(User::factory()->create());

    $this->delete(route('wishlist.destroy', $wishlistItem))->assertForbidden();
});

it('adds a product without variants to the wishlist', function () {
    $product = Product::factory()->create(['name' => 'Canvas Tote Bag', 'price' => 24.99, 'stock' => 8]);
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('wishlist.store'), ['product_id' => $product->id])->assertRedirect();

    $wishlistItem = auth()->user()->wishlistItems()->first();

    expect($wishlistItem->product_id)->toBe($product->id)
        ->and($wishlistItem->product_variant_id)->toBeNull();
});

it('does not duplicate a product without variants on the wishlist', function () {
    $product = Product::factory()->create(['name' => 'Canvas Tote Bag', 'price' => 24.99, 'stock' => 8]);
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('wishlist.store'), ['product_id' => $product->id])->assertRedirect();
    $this->post(route('wishlist.store'), ['product_id' => $product->id])->assertRedirect();

    $this->assertDatabaseCount('wishlist_items', 1);
});

it('lists a variant-less product on the wishlist page', function () {
    $product = Product::factory()->create(['name' => 'Canvas Tote Bag', 'price' => 24.99, 'stock' => 8]);
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->post(route('wishlist.store'), ['product_id' => $product->id])->assertRedirect();

    $this->get(route('wishlist.index'))
        ->assertOk()
        ->assertSee('Canvas Tote Bag')
        ->assertSee('$24.99');
});
