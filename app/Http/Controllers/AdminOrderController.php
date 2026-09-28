<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function updateStatus(
        UpdateOrderStatusRequest $request,
        Order $order,
        InventoryService $inventory,
    ): RedirectResponse {
        $status = OrderStatus::from($request->validated('status'));

        DB::transaction(function () use ($order, $status, $inventory): void {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (
                $status !== OrderStatus::Cancelled
                || $order->status === OrderStatus::Cancelled
                || $order->stock_released_at !== null
            ) {
                $order->update(['status' => $status]);

                return;
            }

            $items = $order->items()
                ->with(['product', 'productVariant'])
                ->get();

            foreach ($items as $item) {
                $stockable = $item->productVariant ?? $item->product;

                if ($item->stock_deducted && $stockable !== null) {
                    $inventory->increment($stockable, $item->quantity);
                }
            }

            $order->update([
                'status' => $status,
                'stock_released_at' => now(),
            ]);
        }, 3);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated.');
    }
}
