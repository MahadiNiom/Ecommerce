<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('variants')->get();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::tree();
        $brands = Brand::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('products.create', compact('categories', 'brands', 'tags'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());
        $product->tags()->sync($request->validated('tags', []));

        return redirect()->route('products.show', $product)->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load(['variants.variantOptions', 'productVariants.variantOptions.variant']);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::tree();
        $brands = Brand::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'brands', 'tags'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());
        $product->tags()->sync($request->validated('tags', []));

        return redirect()->route('products.show', $product)->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
