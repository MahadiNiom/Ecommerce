<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = auth()->user()->orders()->withCount('items')->latest()->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $order->load('items.productVariant');

        return view('orders.show', compact('order'));
    }
}
