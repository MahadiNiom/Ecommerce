<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(InventoryService $inventory): View
    {
        $cart = auth()->user()->cart()->with([
            'items.productVariant.product',
            'items.productVariant.variantOptions.variant',
            'items.product',
        ])->first() ?? new Cart;
        $unavailableItemIds = $cart->items
            ->filter(fn (CartItem $item): bool => ! $inventory->canFulfill($item->productVariant ?? $item->product, $item->quantity))
            ->pluck('id');

        return view('checkout.create', [
            'cart' => $cart,
            'hasUnavailableItems' => $unavailableItemIds->isNotEmpty(),
            'unavailableItemIds' => $unavailableItemIds,
        ]);
    }

    public function store(StoreCheckoutRequest $request, InventoryService $inventory): RedirectResponse
    {
        try {
            $order = DB::transaction(function () use ($request, $inventory): Order {
                $cart = auth()->user()->cart()
                    ->with(['items.productVariant.product', 'items.product'])
                    ->lockForUpdate()
                    ->first();

                abort_if($cart === null || $cart->items->isEmpty(), 404, 'Your cart is empty.');

                $order = auth()->user()->orders()->create([
                    'status' => OrderStatus::Pending,
                    'total' => 0,
                    ...$request->validated(),
                ]);
                $total = 0.0;

                foreach ($cart->items as $cartItem) {
                    $variant = $cartItem->productVariant;
                    $product = $variant?->product ?? $cartItem->product;

                    if ($product === null) {
                        throw ValidationException::withMessages([
                            'cart' => 'An item in your cart is no longer available.',
                        ]);
                    }

                    $stockDeducted = $inventory->decrement($variant ?? $product, $cartItem->quantity);
                    $unitPrice = (float) ($variant?->price ?? $product->price);

                    $order->items()->create([
                        'product_id' => $variant === null ? $product->id : null,
                        'product_variant_id' => $variant?->id,
                        'product_name' => $product->name,
                        'variant_label' => $variant?->combinationLabel() ?? '',
                        'unit_price' => $unitPrice,
                        'quantity' => $cartItem->quantity,
                        'stock_deducted' => $stockDeducted,
                    ]);

                    $total += $unitPrice * $cartItem->quantity;
                }

                $order->update(['total' => $total]);
                $cart->items()->delete();

                return $order;
            }, 3);
        } catch (InsufficientStockException) {
            return back()
                ->withInput()
                ->withErrors(['cart' => 'One or more items no longer have enough stock.']);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully.');
    }
}
