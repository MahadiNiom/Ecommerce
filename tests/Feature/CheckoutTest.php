<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function checkoutPayload(): array
{
    return [
        'shipping_name' => 'Jane Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Springfield',
        'shipping_zip' => '12345',
        'shipping_country' => 'USA',
    ];
}

it('redirects guests away from checkout', function () {
    $this->get(route('checkout.create'))->assertRedirect(route('login'));
});

it('shows an empty cart message on the checkout page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('checkout.create'))
        ->assertOk()
        ->assertSee('Your cart is empty');
});

it('places an order from the cart and clears the cart', function () {
    [, $variant] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->post(route('cart.store'), ['product_variant_id' => $variant->id, 'quantity' => 2])->assertRedirect();

    $this->post(route('checkout.store'), checkoutPayload())
        ->assertRedirect(route('orders.show', Order::first()));

    $order = $user->orders()->first();

    expect($order)->not->toBeNull()
        ->and($order->total)->toBe('39.98')
        ->and($order->shipping_name)->toBe('Jane Doe')
        ->and($order->status)->toBe(OrderStatus::Pending)
        ->and($order->items()->count())->toBe(1);

    $item = $order->items()->first();

    expect($item->product_name)->toBe('Classic T-Shirt')
        ->and($item->variant_label)->toBe('Size: Large')
        ->and($item->unit_price)->toBe('19.99')
        ->and($item->quantity)->toBe(2);

    expect($user->cart->items()->count())->toBe(0);
});

it('places an order for a product without variants and clears the cart', function () {
    $product = Product::factory()->create(['name' => 'Canvas Tote Bag', 'price' => 24.99, 'stock' => 8]);
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2])->assertRedirect();

    $this->post(route('checkout.store'), checkoutPayload())
        ->assertRedirect(route('orders.show', Order::first()));

    $order = $user->orders()->first();

    expect($order)->not->toBeNull()
        ->and($order->total)->toBe('49.98')
        ->and($order->items()->count())->toBe(1);

    $item = $order->items()->first();

    expect($item->product_name)->toBe('Canvas Tote Bag')
        ->and($item->product_variant_id)->toBeNull()
        ->and($item->variant_label)->toBe('')
        ->and($item->unit_price)->toBe('24.99')
        ->and($item->quantity)->toBe(2);

    expect($user->cart->items()->count())->toBe(0);
});

it('returns a 404 when checkout is attempted with an empty cart', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('checkout.store'), checkoutPayload())->assertNotFound();
});

it('lists the authenticated user\'s orders', function () {
    [, $variant] = shopProductWithVariant();
    [$product2] = shopProductWithVariant();

    $user = User::factory()->create();
    $this->actingAs($user);

    $order = $user->orders()->create([...checkoutPayload(), 'total' => 19.99]);
    $order->items()->create([
        'product_variant_id' => $variant->id,
        'product_name' => $product2->name,
        'unit_price' => 19.99,
        'quantity' => 1,
    ]);

    $second = $user->orders()->create([...checkoutPayload(), 'total' => 42.00]);

    $otherUser = User::factory()->create();
    $otherUser->orders()->create([...checkoutPayload(), 'total' => 5]);

    $this->get(route('orders.index'))
        ->assertOk()
        ->assertSee($order->id)
        ->assertSee($second->id)
        ->assertSee('$19.99')
        ->assertSee('$42.00')
        ->assertDontSee('$5.00');

    $this->assertSame(2, $user->orders()->count());
});

it('shows an order with its items', function () {
    [$product, $variant] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);

    $order = $user->orders()->create([...checkoutPayload(), 'total' => 39.98]);
    $order->items()->create([
        'product_variant_id' => $variant->id,
        'product_name' => $product->name,
        'variant_label' => 'Size: Large',
        'unit_price' => 19.99,
        'quantity' => 2,
    ]);

    $this->get(route('orders.show', $order))
        ->assertOk()
        ->assertSee($product->name)
        ->assertSee('Size: Large')
        ->assertSee('$39.98');
});

it('forbids viewing another user\'s order', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $order = User::factory()->create()->orders()->create([...checkoutPayload(), 'total' => 5]);

    $this->get(route('orders.show', $order))->assertForbidden();
});
