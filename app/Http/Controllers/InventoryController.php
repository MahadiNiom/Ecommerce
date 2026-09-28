<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\AdjustStockRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();

        if (! in_array($status, ['all', 'low', 'out', 'untracked'], true)) {
            $status = 'all';
        }

        $allRows = $this->inventoryRows();
        $rows = $allRows->filter(function (array $row) use ($search, $status): bool {
            $normalizedSearch = mb_strtolower($search);
            $searchable = mb_strtolower($row['name'].' '.$row['variant_label']);
            $matchesSearch = $normalizedSearch === '' || str_contains($searchable, $normalizedSearch);

            $matchesStatus = match ($status) {
                'low' => $row['tracked'] && $row['stock'] > 0 && $row['stock'] <= 5,
                'out' => $row['tracked'] && $row['stock'] === 0,
                'untracked' => ! $row['tracked'],
                default => true,
            };

            return $matchesSearch && $matchesStatus;
        })->values();

        return view('admin.inventory.index', [
            'rows' => $rows,
            'summary' => [
                'total' => $allRows->count(),
                'tracked' => $allRows->filter(fn (array $row): bool => $row['tracked'])->count(),
                'low' => $allRows->filter(fn (array $row): bool => $row['tracked'] && $row['stock'] > 0 && $row['stock'] <= 5)->count(),
                'out' => $allRows->filter(fn (array $row): bool => $row['tracked'] && $row['stock'] === 0)->count(),
                'untracked' => $allRows->filter(fn (array $row): bool => ! $row['tracked'])->count(),
            ],
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function adjust(AdjustStockRequest $request, InventoryService $inventory): RedirectResponse
    {
        $validated = $request->validated();
        $stockable = $this->stockable($validated['stockable_type'], (int) $validated['stockable_id']);

        try {
            $inventory->adjust($stockable, $validated['adjustment']);
        } catch (InsufficientStockException) {
            return back()
                ->withInput()
                ->withErrors(['adjustment' => 'The adjustment would make stock negative.']);
        }

        return redirect()->route('admin.inventory.index')->with('success', 'Stock updated.');
    }

    private function inventoryRows(): Collection
    {
        return Product::query()
            ->with(['productVariants.variantOptions.variant'])
            ->orderBy('name')
            ->get()
            ->flatMap(function (Product $product): array {
                if ($product->productVariants->isEmpty()) {
                    return [[
                        'type' => 'product',
                        'id' => $product->id,
                        'name' => $product->name,
                        'variant_label' => 'Base product',
                        'stock' => $product->stock,
                        'tracked' => $product->stock !== null,
                        'edit_url' => route('products.edit', $product),
                    ]];
                }

                return $product->productVariants->map(fn (ProductVariant $productVariant): array => [
                    'type' => 'variant',
                    'id' => $productVariant->id,
                    'name' => $product->name,
                    'variant_label' => $productVariant->combinationLabel(),
                    'stock' => $productVariant->stock,
                    'tracked' => true,
                    'edit_url' => route('products.product-variants.edit', [$product, $productVariant]),
                ])->all();
            })
            ->values();
    }

    private function stockable(string $type, int $id): Product|ProductVariant
    {
        return match ($type) {
            'product' => Product::findOrFail($id),
            'variant' => ProductVariant::findOrFail($id),
            default => throw ValidationException::withMessages([
                'stockable_type' => 'Select a valid stockable type.',
            ]),
        };
    }
}
