<?php

use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\VariantController;
use App\Http\Controllers\VariantOptionController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product}', [ShopController::class, 'show'])->name('shop.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{wishlistItem}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::get('/admin/orders', [AdminOrderController::class, 'index'])
        ->middleware('permission:view orders')
        ->name('admin.orders.index');
    Route::get('/admin/orders/{order}', [AdminOrderController::class, 'show'])
        ->middleware('permission:view orders')
        ->name('admin.orders.show');
    Route::patch('/admin/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
        ->middleware('permission:update order status')
        ->name('admin.orders.status.update');

    Route::get('/admin/inventory', [InventoryController::class, 'index'])
        ->middleware('permission:view products')
        ->name('admin.inventory.index');
    Route::patch('/admin/inventory/adjust', [InventoryController::class, 'adjust'])
        ->middleware('permission:edit products')
        ->name('admin.inventory.adjust');

    Route::resource('products', ProductController::class)
        ->middlewareFor(['index', 'show'], 'permission:view products')
        ->middlewareFor(['create', 'store'], 'permission:create products')
        ->middlewareFor(['edit', 'update'], 'permission:edit products')
        ->middlewareFor('destroy', 'permission:delete products');
    Route::resource('products.variants', VariantController::class)
        ->scoped()
        ->middlewareFor(['index', 'show'], 'permission:view products')
        ->middlewareFor(['create', 'store'], 'permission:edit products')
        ->middlewareFor(['edit', 'update'], 'permission:edit products')
        ->middlewareFor('destroy', 'permission:delete products');
    Route::resource('variants.variant-options', VariantOptionController::class)
        ->scoped()
        ->middlewareFor(['index', 'show'], 'permission:view products')
        ->middlewareFor(['create', 'store'], 'permission:edit products')
        ->middlewareFor(['edit', 'update'], 'permission:edit products')
        ->middlewareFor('destroy', 'permission:delete products');
    Route::get('/products/{product}/product-variants/assign', [ProductVariantController::class, 'assign'])
        ->middleware('permission:edit products')
        ->name('products.product-variants.assign');
    Route::post('/products/{product}/product-variants/assign', [ProductVariantController::class, 'storeAssign'])
        ->middleware('permission:edit products')
        ->name('products.product-variants.assign.store');
    Route::resource('products.product-variants', ProductVariantController::class)
        ->scoped()
        ->middlewareFor(['index', 'show'], 'permission:view products')
        ->middlewareFor(['create', 'store'], 'permission:edit products')
        ->middlewareFor(['edit', 'update'], 'permission:edit products')
        ->middlewareFor('destroy', 'permission:delete products');
    Route::resource('categories', CategoryController::class)
        ->middlewareFor(['index', 'show'], 'permission:view categories')
        ->middlewareFor(['create', 'store'], 'permission:create categories')
        ->middlewareFor(['edit', 'update'], 'permission:edit categories')
        ->middlewareFor('destroy', 'permission:delete categories');
    Route::resource('brands', BrandController::class)
        ->middlewareFor(['index', 'show'], 'permission:view brands')
        ->middlewareFor(['create', 'store'], 'permission:create brands')
        ->middlewareFor(['edit', 'update'], 'permission:edit brands')
        ->middlewareFor('destroy', 'permission:delete brands');
    Route::resource('tags', TagController::class)
        ->middlewareFor(['index', 'show'], 'permission:view tags')
        ->middlewareFor(['create', 'store'], 'permission:create tags')
        ->middlewareFor(['edit', 'update'], 'permission:edit tags')
        ->middlewareFor('destroy', 'permission:delete tags');

    Route::resource('roles', RoleController::class)
        ->middlewareFor(['index', 'show'], 'permission:view roles')
        ->middlewareFor(['create', 'store'], 'permission:create roles')
        ->middlewareFor(['edit', 'update'], 'permission:edit roles')
        ->middlewareFor('destroy', 'permission:delete roles');
    Route::resource('permissions', PermissionController::class)
        ->middlewareFor(['index', 'show'], 'permission:view permissions')
        ->middlewareFor(['create', 'store'], 'permission:create permissions')
        ->middlewareFor(['edit', 'update'], 'permission:edit permissions')
        ->middlewareFor('destroy', 'permission:delete permissions');
    Route::get('users', [UserRoleController::class, 'index'])
        ->middleware('permission:view users')
        ->name('users.index');
    Route::get('users/{user}/roles', [UserRoleController::class, 'edit'])
        ->middleware('permission:assign roles')
        ->name('users.roles.edit');
    Route::put('users/{user}/roles', [UserRoleController::class, 'update'])
        ->middleware('permission:assign roles')
        ->name('users.roles.update');
});

require __DIR__.'/auth.php';
