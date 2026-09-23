<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

function orderManagementActingAdmin(): User
{
    $admin = User::factory()->create();

    foreach (Permissions::orders() as $name) {
        $admin->givePermissionTo(Permission::findOrCreate($name));
    }

    test()->actingAs($admin);

    return $admin;
}

function adminOrderWithItem(): Order
{
    $order = Order::factory()->create(['total' => 24.99]);
    $order->items()->create([
        'product_name' => 'Canvas Tote Bag',
        'unit_price' => 24.99,
        'quantity' => 1,
    ]);

    return $order;
}

it('forbids managing orders without the permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('admin.orders.index'))->assertForbidden();
});

it('lists all orders for an admin', function () {
    orderManagementActingAdmin();
    $order = adminOrderWithItem();

    $this->get(route('admin.orders.index'))
        ->assertOk()
        ->assertSee($order->user->email)
        ->assertSee('$24.99');
});

it('filters orders by status', function () {
    orderManagementActingAdmin();
    Order::factory()->create(['status' => OrderStatus::Pending, 'total' => 5.00]);
    Order::factory()->create(['status' => OrderStatus::Shipped, 'total' => 15.00]);

    $this->get(route('admin.orders.index', ['status' => 'shipped']))
        ->assertOk()
        ->assertSee('$15.00')
        ->assertDontSee('$5.00');
});

it('shows an order with customer, shipping, and items', function () {
    orderManagementActingAdmin();
    $order = adminOrderWithItem();
    $order->update(['shipping_name' => 'Jane Doe']);

    $this->get(route('admin.orders.show', $order))
        ->assertOk()
        ->assertSee('Jane Doe')
        ->assertSee($order->user->email)
        ->assertSee('Canvas Tote Bag')
        ->assertSee('$24.99');
});

it('updates the order status', function () {
    orderManagementActingAdmin();
    $order = Order::factory()->create();

    $this->patch(route('admin.orders.status.update', $order), ['status' => 'shipped'])
        ->assertRedirect(route('admin.orders.show', $order));

    expect($order->refresh()->status)->toBe(OrderStatus::Shipped);
});

it('rejects an invalid order status', function () {
    orderManagementActingAdmin();
    $order = Order::factory()->create();

    $this->patch(route('admin.orders.status.update', $order), ['status' => 'delivered-tomorrow'])
        ->assertSessionHasErrors('status');

    expect($order->refresh()->status)->toBe(OrderStatus::Pending);
});
