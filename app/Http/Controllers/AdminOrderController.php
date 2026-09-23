<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $order->update($request->validated());

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated.');
    }
}
