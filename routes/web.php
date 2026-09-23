<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\VariantController;
use App\Http\Controllers\VariantOptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('permission:manage products')->group(function () {
        Route::resource('products', ProductController::class);
        Route::resource('products.variants', VariantController::class)->scoped();
        Route::resource('variants.variant-options', VariantOptionController::class)->scoped();
        Route::get('/products/{product}/product-variants/assign', [ProductVariantController::class, 'assign'])
            ->name('products.product-variants.assign');
        Route::post('/products/{product}/product-variants/assign', [ProductVariantController::class, 'storeAssign'])
            ->name('products.product-variants.assign.store');
        Route::resource('products.product-variants', ProductVariantController::class)->scoped();
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
