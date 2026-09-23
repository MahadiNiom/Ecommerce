<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVariantRequest;
use App\Http\Requests\UpdateVariantRequest;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VariantController extends Controller
{
    public function index(Product $product): View
    {
        $variants = $product->variants()->with('variantOptions')->get();

        return view('variants.index', compact('product', 'variants'));
    }

    public function create(Product $product): View
    {
        return view('variants.create', compact('product'));
    }

    public function store(StoreVariantRequest $request, Product $product): RedirectResponse
    {
        $product->variants()->create($request->validated());

        return redirect()->route('products.variants.index', $product)->with('success', 'Variant created successfully.');
    }

    public function show(Product $product, Variant $variant): View
    {
        $variant->load('variantOptions');

        return view('variants.show', compact('product', 'variant'));
    }

    public function edit(Product $product, Variant $variant): View
    {
        return view('variants.edit', compact('product', 'variant'));
    }

    public function update(UpdateVariantRequest $request, Product $product, Variant $variant): RedirectResponse
    {
        $variant->update($request->validated());

        return redirect()->route('products.variants.index', $product)->with('success', 'Variant updated successfully.');
    }

    public function destroy(Product $product, Variant $variant): RedirectResponse
    {
        $variant->delete();

        return redirect()->route('products.variants.index', $product)->with('success', 'Variant deleted successfully.');
    }
}
