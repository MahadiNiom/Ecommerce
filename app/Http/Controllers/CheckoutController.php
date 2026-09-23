<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(): View
    {
        $cart = auth()->user()->cart()->with([
            'items.productVariant.product',
            'items.productVariant.variantOptions.variant',
            'items.product',
        ])->first() ?? new Cart;

        return view('checkout.create', compact('cart'));
    }

    public function store(StoreCheckoutRequest $request): RedirectResponse
    {
        $cart = auth()->user()->cart()->with(['items.productVariant.product', 'items.product'])->first();

        abort_if($cart === null || $cart->items->isEmpty(), 404, 'Your cart is empty.');

        $order = auth()->user()->orders()->create([
            'status' => OrderStatus::Pending,
            'total' => $cart->total(),
            ...$request->validated(),
        ]);

        foreach ($cart->items as $cartItem) {
            $variant = $cartItem->productVariant;
            $product = $variant?->product ?? $cartItem->product;
            $order->items()->create([
                'product_variant_id' => $variant?->id,
                'product_name' => $product->name,
                'variant_label' => $variant?->combinationLabel() ?? '',
                'unit_price' => $variant?->price ?? $product->price,
                'quantity' => $cartItem->quantity,
            ]);
        }

        $cart->items()->delete();

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully.');
    }
}
