<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishlistItemRequest;
use App\Models\WishlistItem;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(InventoryService $inventory): View
    {
        $wishlist = auth()->user()->wishlistItems()
            ->with(['productVariant.product', 'productVariant.variantOptions.variant', 'product'])
            ->get();
        $availability = $wishlist->mapWithKeys(fn (WishlistItem $item): array => [
            $item->id => $inventory->canFulfill($item->productVariant ?? $item->product, 1),
        ]);

        return view('wishlist.index', [
            'wishlist' => $wishlist,
            'availability' => $availability,
        ]);
    }

    public function store(StoreWishlistItemRequest $request): RedirectResponse
    {
        auth()->user()->wishlistItems()->firstOrCreate([
            'product_variant_id' => $request->validated('product_variant_id'),
            'product_id' => $request->validated('product_id'),
        ]);

        return redirect()->back()->with('success', 'Added to wishlist.');
    }

    public function destroy(WishlistItem $wishlistItem): RedirectResponse
    {
        abort_unless($wishlistItem->user_id === auth()->id(), 403);

        $wishlistItem->delete();

        return redirect()->route('wishlist.index')->with('success', 'Removed from wishlist.');
    }
}
