<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $term = trim($request->string('q')->toString());

        $products = Product::query()
            ->with(['category', 'brand', 'tags', 'productVariants'])
            ->when($term !== '', fn ($query) => $query->matchingSearchTerm($term))
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->integer('category')))
            ->when($request->filled('brand'), fn ($query) => $query->where('brand_id', $request->integer('brand')))
            ->when($request->filled('tag'), fn ($query) => $query->whereHas('tags', fn ($query) => $query->where('tags.id', $request->integer('tag'))))
            ->orderBy('name')
            ->get();

        return view('shop.index', [
            'products' => $products,
            'categories' => Category::tree(),
            'brands' => Brand::withCount('products')->orderBy('name')->get(),
            'tags' => Tag::withCount('products')->orderBy('name')->get(),
            'filters' => $request->only(['q', 'category', 'brand', 'tag']),
        ]);
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'brand', 'tags', 'variants.variantOptions', 'productVariants.variantOptions.variant']);

        return view('shop.show', compact('product'));
    }
}
