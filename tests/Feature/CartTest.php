<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests away from the cart', function () {
    $this->get(route('cart.index'))->assertRedirect(route('login'));
});

it('shows an empty cart to an authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('cart.index'))
        ->assertOk()
        ->assertSee('Your cart is empty');
});

it('adds an item to the cart', function () {
    [, $variant] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('cart.store'), [
        'product_variant_id' => $variant->id,
        'quantity' => 1,
    ])->assertRedirect();

    $item = auth()->user()->cart->items()->first();

    expect($item->product_variant_id)->toBe($variant->id)
        ->and($item->quantity)->toBe(1);
});

it('increments quantity when the same variant is added again', function () {
    [, $variant] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('cart.store'), ['product_variant_id' => $variant->id, 'quantity' => 2])->assertRedirect();
    $this->post(route('cart.store'), ['product_variant_id' => $variant->id, 'quantity' => 1])->assertRedirect();

    expect(auth()->user()->cart->items()->first()->quantity)->toBe(3);
});

it('updates the quantity of a cart item', function () {
    [, $variant] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->post(route('cart.store'), ['product_variant_id' => $variant->id, 'quantity' => 1])->assertRedirect();

    $item = auth()->user()->cart->items()->first();

    $this->put(route('cart.update', $item), ['quantity' => 4])
        ->assertRedirect(route('cart.index'));

    expect($item->refresh()->quantity)->toBe(4);
});

it('removes an item from the cart', function () {
    [, $variant] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->post(route('cart.store'), ['product_variant_id' => $variant->id, 'quantity' => 1])->assertRedirect();

    $item = auth()->user()->cart->items()->first();

    $this->delete(route('cart.destroy', $item))
        ->assertRedirect(route('cart.index'));

    $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
});

it('rejects updating a cart item that belongs to another user', function () {
    [, $variant] = shopProductWithVariant();
    $owner = User::factory()->create();
    $this->actingAs($owner);
    $this->post(route('cart.store'), ['product_variant_id' => $variant->id, 'quantity' => 1])->assertRedirect();
    $item = $owner->cart->items()->first();

    $this->actingAs(User::factory()->create());

    $this->put(route('cart.update', $item), ['quantity' => 2])->assertForbidden();
    $this->delete(route('cart.destroy', $item))->assertForbidden();
});

it('rejects adding a variant that does not exist', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('cart.store'), ['product_variant_id' => 999, 'quantity' => 1])
        ->assertSessionHasErrors('product_variant_id');
});

it('adds a product without variants to the cart', function () {
    $product = Product::factory()->create(['name' => 'Canvas Tote Bag', 'price' => 24.99, 'stock' => 8]);
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 2,
    ])->assertRedirect();

    $item = auth()->user()->cart->items()->first();

    expect($item->product_id)->toBe($product->id)
        ->and($item->product_variant_id)->toBeNull()
        ->and($item->quantity)->toBe(2);
});

it('increments quantity when the same product without variants is added again', function () {
    $product = Product::factory()->create(['name' => 'Canvas Tote Bag', 'price' => 24.99, 'stock' => 8]);
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2])->assertRedirect();
    $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1])->assertRedirect();

    expect(auth()->user()->cart->items()->first()->quantity)->toBe(3);
});

it('rejects adding both a product and a variant at once', function () {
    [$product, $variant] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('cart.store'), [
        'product_id' => $product->id,
        'product_variant_id' => $variant->id,
        'quantity' => 1,
    ])->assertSessionHasErrors('product_id');

    $this->assertDatabaseCount('cart_items', 0);
});

it('rejects adding a product that has variants', function () {
    [$product] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1])
        ->assertSessionHasErrors('product_id');
});

it('rejects adding a product without a price', function () {
    $product = Product::factory()->create(['name' => 'Gift']);
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1])
        ->assertSessionHasErrors('product_id');
});
