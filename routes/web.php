<?php

use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
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
});

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

    Route::middleware('permission:manage orders')->group(function () {
        Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/admin/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::patch('/admin/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('admin.orders.status.update');
    });

    Route::middleware('permission:manage products')->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('products.variants', VariantController::class)->scoped();
        Route::resource('variants.variant-options', VariantOptionController::class)->scoped();
        Route::get('/products/{product}/product-variants/assign', [ProductVariantController::class, 'assign'])
            ->name('products.product-variants.assign');
        Route::post('/products/{product}/product-variants/assign', [ProductVariantController::class, 'storeAssign'])
            ->name('products.product-variants.assign.store');
        Route::resource('products.product-variants', ProductVariantController::class)->scoped();
        Route::resource('categories', CategoryController::class);
        Route::resource('brands', BrandController::class);
        Route::resource('tags', TagController::class);
    });

    Route::middleware('permission:manage roles and permissions')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::get('users', [UserRoleController::class, 'index'])->name('users.index');
        Route::get('users/{user}/roles', [UserRoleController::class, 'edit'])->name('users.roles.edit');
        Route::put('users/{user}/roles', [UserRoleController::class, 'update'])->name('users.roles.update');
    });
});

require __DIR__.'/auth.php';
