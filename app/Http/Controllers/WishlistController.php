<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishlistItemRequest;
use App\Models\WishlistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $wishlist = auth()->user()->wishlistItems()
            ->with(['productVariant.product', 'productVariant.variantOptions.variant', 'product'])
            ->get();

        return view('wishlist.index', compact('wishlist'));
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
