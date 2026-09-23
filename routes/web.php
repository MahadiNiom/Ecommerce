<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\VariantController;
use App\Http\Controllers\VariantOptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('products', ProductController::class);
Route::resource('products.variants', VariantController::class)->scoped();
Route::resource('variants.variant-options', VariantOptionController::class)->scoped();
Route::get('/products/{product}/product-variants/assign', [ProductVariantController::class, 'assign'])->name('products.product-variants.assign');
Route::post('/products/{product}/product-variants/assign', [ProductVariantController::class, 'storeAssign'])->name('products.product-variants.assign.store');
Route::resource('products.product-variants', ProductVariantController::class)->scoped();
