<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

function inventoryUserWithPermissions(string ...$permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        $user->givePermissionTo(Permission::findOrCreate($permission));
    }

    return $user;
}

function inventoryCheckoutPayload(): array
{
    return [
        'shipping_name' => 'Jane Doe',
        'shipping_address' => '123 Main St',
        'shipping_city' => 'Springfield',
        'shipping_zip' => '12345',
        'shipping_country' => 'USA',
    ];
}

it('rejects adding more cart items than tracked stock', function () {
    $product = Product::factory()->create(['price' => 24.99, 'stock' => 1]);
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('cart.store'), [
        'product_id' => $product->id,
        'quantity' => 2,
    ])->assertSessionHasErrors('quantity');

    $this->assertDatabaseCount('cart_items', 0);
    expect($product->refresh()->stock)->toBe(1);
});

it('rejects increasing a cart item beyond tracked stock', function () {
    $product = Product::factory()->create(['price' => 24.99, 'stock' => 2]);
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1])->assertRedirect();

    $item = $user->cart->items()->first();

    $this->put(route('cart.update', $item), ['quantity' => 3])
        ->assertSessionHasErrors('quantity');

    expect($item->refresh()->quantity)->toBe(1)
        ->and($product->refresh()->stock)->toBe(2);
});

it('allows an untracked direct product to be ordered without changing its stock', function () {
    $product = Product::factory()->create(['name' => 'Canvas Tote Bag', 'price' => 24.99, 'stock' => null]);
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2])->assertRedirect();

    $this->post(route('checkout.store'), inventoryCheckoutPayload())->assertRedirect();

    $order = $user->orders()->first();
    $item = $order->items()->first();

    expect($item->product_id)->toBe($product->id)
        ->and($item->product_variant_id)->toBeNull()
        ->and($item->stock_deducted)->toBeFalse()
        ->and($product->refresh()->stock)->toBeNull()
        ->and($user->cart->items()->count())->toBe(0);
});

it('deducts tracked variant stock when placing an order', function () {
    [, $variant] = shopProductWithVariant();
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->post(route('cart.store'), ['product_variant_id' => $variant->id, 'quantity' => 2])->assertRedirect();

    $this->post(route('checkout.store'), inventoryCheckoutPayload())->assertRedirect();

    expect($variant->refresh()->stock)->toBe(3)
        ->and($user->orders()->first()->items()->first()->product_variant_id)->toBe($variant->id);
});

it('rolls back the order and previous deductions when checkout stock is insufficient', function () {
    $firstProduct = Product::factory()->create(['price' => 10.00, 'stock' => 2]);
    $secondProduct = Product::factory()->create(['price' => 15.00, 'stock' => 2]);
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->post(route('cart.store'), ['product_id' => $firstProduct->id, 'quantity' => 1])->assertRedirect();
    $this->post(route('cart.store'), ['product_id' => $secondProduct->id, 'quantity' => 1])->assertRedirect();
    $secondProduct->update(['stock' => 0]);

    $this->post(route('checkout.store'), inventoryCheckoutPayload())
        ->assertSessionHasErrors('cart');

    $this->assertDatabaseCount('orders', 0);
    expect($firstProduct->refresh()->stock)->toBe(2)
        ->and($secondProduct->refresh()->stock)->toBe(0)
        ->and($user->cart->items()->count())->toBe(2);
});

it('restores variant and direct product stock once when an order is cancelled', function () {
    $customer = User::factory()->create();
    $directProduct = Product::factory()->create(['price' => 24.99, 'stock' => 5]);
    [$variantProduct, $variant] = shopProductWithVariant();
    $order = Order::factory()->create(['user_id' => $customer->id]);
    $order->items()->create([
        'product_id' => $directProduct->id,
        'product_name' => $directProduct->name,
        'unit_price' => 24.99,
        'quantity' => 2,
        'stock_deducted' => true,
    ]);
    $order->items()->create([
        'product_variant_id' => $variant->id,
        'product_name' => $variantProduct->name,
        'variant_label' => 'Size: Large',
        'unit_price' => 19.99,
        'quantity' => 3,
        'stock_deducted' => true,
    ]);
    $admin = inventoryUserWithPermissions(Permissions::UPDATE_ORDER_STATUS);
    $this->actingAs($admin);

    $this->patch(route('admin.orders.status.update', $order), ['status' => OrderStatus::Cancelled->value])
        ->assertRedirect(route('admin.orders.show', $order));

    expect($directProduct->refresh()->stock)->toBe(7)
        ->and($variant->refresh()->stock)->toBe(8)
        ->and($order->refresh()->status)->toBe(OrderStatus::Cancelled)
        ->and($order->stock_released_at)->not->toBeNull();

    $this->patch(route('admin.orders.status.update', $order), ['status' => OrderStatus::Cancelled->value])
        ->assertRedirect(route('admin.orders.show', $order));

    expect($directProduct->refresh()->stock)->toBe(7)
        ->and($variant->refresh()->stock)->toBe(8);
});

it('does not restore an untracked product after its stock is initialized', function () {
    $product = Product::factory()->create(['price' => 24.99, 'stock' => null]);
    $customer = User::factory()->create();
    $this->actingAs($customer);
    $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2])->assertRedirect();
    $this->post(route('checkout.store'), inventoryCheckoutPayload())->assertRedirect();

    $order = $customer->orders()->first();
    $product->update(['stock' => 4]);
    $admin = inventoryUserWithPermissions(Permissions::UPDATE_ORDER_STATUS);
    $this->actingAs($admin);

    $this->patch(route('admin.orders.status.update', $order), ['status' => OrderStatus::Cancelled->value])
        ->assertRedirect(route('admin.orders.show', $order));

    expect($product->refresh()->stock)->toBe(4)
        ->and($order->refresh()->stock_released_at)->not->toBeNull();
});

it('requires the appropriate product permissions for inventory access and adjustment', function () {
    $product = Product::factory()->create(['price' => 24.99, 'stock' => null]);
    $unprivileged = User::factory()->create();
    $this->actingAs($unprivileged);
    $this->get(route('admin.inventory.index'))->assertForbidden();

    $viewer = inventoryUserWithPermissions(Permissions::VIEW_PRODUCTS);
    $this->actingAs($viewer);
    $this->get(route('admin.inventory.index'))
        ->assertOk()
        ->assertSee('Not tracked');
    $this->patch(route('admin.inventory.adjust'), [
        'stockable_type' => 'product',
        'stockable_id' => $product->id,
        'adjustment' => 1,
    ])->assertForbidden();

    $editor = inventoryUserWithPermissions(Permissions::EDIT_PRODUCTS);
    $this->actingAs($editor);
    $this->patch(route('admin.inventory.adjust'), [
        'stockable_type' => 'product',
        'stockable_id' => $product->id,
        'adjustment' => 5,
    ])->assertRedirect(route('admin.inventory.index'));

    expect($product->refresh()->stock)->toBe(5);
});

it('rejects invalid inventory adjustments and base products with variants', function () {
    $product = Product::factory()->create(['price' => 24.99, 'stock' => 2]);
    [$productWithVariant] = shopProductWithVariant();
    $admin = inventoryUserWithPermissions(Permissions::EDIT_PRODUCTS);
    $this->actingAs($admin);

    $this->patch(route('admin.inventory.adjust'), [
        'stockable_type' => 'product',
        'stockable_id' => $product->id,
        'adjustment' => 0,
    ])->assertSessionHasErrors('adjustment');

    $this->patch(route('admin.inventory.adjust'), [
        'stockable_type' => 'product',
        'stockable_id' => $productWithVariant->id,
        'adjustment' => 1,
    ])->assertSessionHasErrors('stockable_id');

    expect($product->refresh()->stock)->toBe(2);
});

it('rejects an adjustment that would make tracked stock negative', function () {
    $product = Product::factory()->create(['price' => 24.99, 'stock' => 2]);
    $admin = inventoryUserWithPermissions(Permissions::EDIT_PRODUCTS);
    $this->actingAs($admin);

    $this->patch(route('admin.inventory.adjust'), [
        'stockable_type' => 'product',
        'stockable_id' => $product->id,
        'adjustment' => -3,
    ])->assertSessionHasErrors('adjustment');

    expect($product->refresh()->stock)->toBe(2);
});

it('shows untracked products as available in the storefront and wishlist', function () {
    $product = Product::factory()->create(['name' => 'Canvas Tote Bag', 'price' => 24.99, 'stock' => null]);
    $user = User::factory()->create();

    $this->get(route('shop.index'))
        ->assertOk()
        ->assertSee('Canvas Tote Bag')
        ->assertSee('Not tracked');

    $this->actingAs($user);
    $this->get(route('shop.show', $product))
        ->assertOk()
        ->assertSee('Stock not tracked')
        ->assertSee('Add to cart');
    $this->post(route('wishlist.store'), ['product_id' => $product->id])->assertRedirect();
    $this->get(route('wishlist.index'))
        ->assertOk()
        ->assertSee('Not tracked')
        ->assertSee('Add to cart');
});
