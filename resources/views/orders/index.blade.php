@extends('base')

@section('title', 'Your Orders - ' . config('app.name'))

@section('content')
    <h1 class="page-title animate-fade-in-up">Your Orders</h1>

    @if ($orders->isEmpty())
        <div class="card mt-8 flex flex-col items-center gap-4 px-6 py-16 text-center animate-fade-in-up">
            <span class="text-6xl" aria-hidden="true">&#128230;</span>
            <h2 class="text-lg font-bold text-stone-900">No orders yet</h2>
            <p class="max-w-sm text-sm text-stone-500">When you place your first order it will show up here.</p>
            <a href="{{ route('shop.index') }}" class="btn-primary">Start shopping</a>
        </div>
    @else
        <div class="table-wrap mt-8 animate-fade-in-up">
            <table class="table">
                <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th class="text-right">View</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="font-mono font-semibold text-stone-900">#{{ $order->id }}</td>
                        <td class="text-stone-500">{{ $order->created_at->format('M j, Y') }}</td>
                        <td><x-order-status-badge :status="$order->status" /></td>
                        <td>{{ $order->items_count }}</td>
                        <td class="font-bold text-emerald-700">${{ $order->total }}</td>
                        <td class="text-right">
                            <a href="{{ route('orders.show', $order) }}" class="btn-outline btn-sm">
                                View
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection