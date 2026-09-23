<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = $this->cart();
        $cart->load([
            'items.productVariant.product',
            'items.productVariant.variantOptions.variant',
            'items.product',
        ]);

        return view('cart.index', compact('cart'));
    }

    public function store(StoreCartItemRequest $request): RedirectResponse
    {
        $cart = $this->cart();

        $cartItem = CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'product_variant_id' => $request->validated('product_variant_id'),
            'product_id' => $request->validated('product_id'),
        ]);
        $cartItem->quantity = $cartItem->exists
            ? $cartItem->quantity + $request->validated('quantity')
            : $request->validated('quantity');
        $cartItem->save();

        return redirect()->back()->with('success', 'Added to cart.');
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->cart->user_id === auth()->id(), 403);

        $cartItem->update($request->validated());

        return redirect()->route('cart.index')->with('success', 'Cart updated.');
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->cart->user_id === auth()->id(), 403);

        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    private function cart(): Cart
    {
        return auth()->user()->cart()->firstOrCreate();
    }
}
