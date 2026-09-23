<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignProductVariantsRequest;
use App\Http\Requests\StoreProductVariantRequest;
use App\Http\Requests\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\View\View;

class ProductVariantController extends Controller
{
    public function index(Product $product): View
    {
        $productVariants = $product->productVariants()->with('variantOptions.variant')->get();

        return view('product-variants.index', compact('product', 'productVariants'));
    }

    public function create(Product $product): View
    {
        return view('product-variants.create', [
            'product' => $product,
            'variants' => $this->variantsWithOptions($product),
        ]);
    }

    public function store(StoreProductVariantRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        $productVariant = $product->productVariants()->create([
            'price' => $validated['price'],
            'stock' => $validated['stock'],
        ]);

        $productVariant->variantOptions()->attach($validated['options']);

        return redirect()->route('products.product-variants.index', $product)->with('success', 'Product variant created successfully.');
    }

    public function show(Product $product, ProductVariant $productVariant): View
    {
        $productVariant->load('variantOptions.variant');

        return view('product-variants.show', compact('product', 'productVariant'));
    }

    public function edit(Product $product, ProductVariant $productVariant): View
    {
        $productVariant->load('variantOptions');

        return view('product-variants.edit', [
            'product' => $product,
            'productVariant' => $productVariant,
            'variants' => $this->variantsWithOptions($product),
        ]);
    }

    public function update(UpdateProductVariantRequest $request, Product $product, ProductVariant $productVariant): RedirectResponse
    {
        $validated = $request->validated();

        $productVariant->update([
            'price' => $validated['price'],
            'stock' => $validated['stock'],
        ]);

        $productVariant->variantOptions()->sync($validated['options']);

        return redirect()->route('products.product-variants.index', $product)->with('success', 'Product variant updated successfully.');
    }

    public function destroy(Product $product, ProductVariant $productVariant): RedirectResponse
    {
        $productVariant->delete();

        return redirect()->route('products.product-variants.index', $product)->with('success', 'Product variant deleted successfully.');
    }

    public function assign(Product $product): View
    {
        return view('product-variants.assign', [
            'product' => $product,
            'combinations' => $this->combinationsFor($product),
            'existing' => $this->existingCombinations($product),
        ]);
    }

    public function storeAssign(AssignProductVariantsRequest $request, Product $product): RedirectResponse
    {
        $existing = $this->existingCombinations($product);

        foreach ($request->validated('combinations') as $data) {
            $optionIds = collect(explode(',', (string) $data['option_ids']))
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();

            $key = $optionIds->implode('-');
            $productVariant = $existing->get($key);

            if ((int) ($data['selected'] ?? 0) === 1) {
                $attributes = [
                    'price' => $data['price'],
                    'stock' => $data['stock'],
                ];

                if ($productVariant === null) {
                    $productVariant = $product->productVariants()->create($attributes);
                    $productVariant->variantOptions()->attach($optionIds->all());
                } else {
                    $productVariant->update($attributes);
                }
            } elseif ($productVariant !== null) {
                $productVariant->delete();
            }
        }

        return redirect()->route('products.show', $product)->with('success', 'Product variants updated successfully.');
    }

    /**
     * All option combinations for the product's variants, keyed by sorted option ids.
     *
     * @return array<string, array{key: string, option_ids: array<int, int>, pairs: list<array{variant_id: int, variant_name: string, option_name: string, option_id: int}>}>
     */
    private function combinationsFor(Product $product): array
    {
        $groups = $this->variantsWithOptions($product)
            ->map(fn ($variant) => $variant->variantOptions->map(fn ($option) => [
                'option_id' => $option->id,
                'variant_id' => $variant->id,
                'variant_name' => $variant->name,
                'option_name' => $option->name,
            ]))
            ->filter(fn ($options) => $options->isNotEmpty())
            ->values();

        if ($groups->isEmpty()) {
            return [];
        }

        $combinations = [];

        $walk = function (array $prefix, SupportCollection $remaining) use (&$walk, &$combinations): void {
            $group = $remaining->first();
            $rest = $remaining->slice(1)->values();

            foreach ($group as $option) {
                $combination = [...$prefix, $option];

                if ($rest->isEmpty()) {
                    $optionIds = collect($combination)->pluck('option_id')->sort()->values();

                    $combinations[$optionIds->implode('-')] = [
                        'key' => $optionIds->implode('-'),
                        'option_ids' => $optionIds->all(),
                        'pairs' => $combination,
                    ];
                } else {
                    $walk($combination, $rest);
                }
            }
        };

        $walk([], $groups);

        return $combinations;
    }

    /**
     * The product's existing variants keyed by their sorted option ids.
     *
     * @return Collection<string, ProductVariant>
     */
    private function existingCombinations(Product $product): Collection
    {
        return $product->productVariants()
            ->with('variantOptions')
            ->get()
            ->keyBy(fn (ProductVariant $productVariant) => $productVariant->variantOptions
                ->pluck('id')
                ->sort()
                ->values()
                ->implode('-'));
    }

    private function variantsWithOptions(Product $product): Collection
    {
        return $product->variants()->with('variantOptions')->get();
    }
}
