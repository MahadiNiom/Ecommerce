<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(InventoryService $inventory): View
    {
        $cart = $this->cart();
        $cart->load([
            'items.productVariant.product',
            'items.productVariant.variantOptions.variant',
            'items.product',
        ]);
        $availability = $cart->items->mapWithKeys(fn (CartItem $item): array => [
            $item->id => $inventory->canFulfill($item->productVariant ?? $item->product, $item->quantity),
        ]);

        return view('cart.index', [
            'cart' => $cart,
            'availability' => $availability,
            'hasUnavailableItems' => $availability->contains(fn (bool $available): bool => ! $available),
        ]);
    }

    public function store(StoreCartItemRequest $request, InventoryService $inventory): RedirectResponse
    {
        $cart = $this->cart();
        $productId = $request->validated('product_id');
        $variantId = $request->validated('product_variant_id');
        $requestedQuantity = $request->validated('quantity');
        $stockable = $this->stockable($productId, $variantId);

        $cartItem = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'product_variant_id' => $variantId,
            'product_id' => $productId,
        ]);
        $quantity = $cartItem->exists
            ? $cartItem->quantity + $requestedQuantity
            : $requestedQuantity;

        if ($quantity > 99) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => 'The maximum quantity is 99.']);
        }

        if (! $inventory->hasAvailableStock($stockable, $quantity)) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => 'The requested quantity is not available.']);
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return redirect()->back()->with('success', 'Added to cart.');
    }

    public function update(
        UpdateCartItemRequest $request,
        CartItem $cartItem,
        InventoryService $inventory,
    ): RedirectResponse {
        abort_unless($cartItem->cart->user_id === auth()->id(), 403);

        $stockable = $cartItem->productVariant ?? $cartItem->product;
        $quantity = $request->validated('quantity');

        if ($stockable === null) {
            return back()->withErrors(['quantity' => 'This item is no longer available.']);
        }

        if (! $inventory->hasAvailableStock($stockable, $quantity)) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => 'The requested quantity is not available.']);
        }

        $cartItem->update($request->validated());

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->cart->user_id === auth()->id(), 403);

        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    private function stockable(?int $productId, ?int $variantId): Product|ProductVariant
    {
        return $variantId !== null
            ? ProductVariant::findOrFail($variantId)
            : Product::findOrFail($productId);
    }

    private function cart(): Cart
    {
        return auth()->user()->cart()->firstOrCreate();
    }
}
