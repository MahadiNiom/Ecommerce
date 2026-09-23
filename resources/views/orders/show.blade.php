@extends('base')

@section('title', 'Order #' . $order->id . ' - ' . config('app.name'))

@section('content')
    <a href="{{ route('orders.index') }}" class="link inline-flex items-center gap-1 text-sm animate-fade-in-up">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back to orders
    </a>

    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="page-title">Order #{{ $order->id }}</h1>
            <p class="page-subtitle">Placed on {{ $order->created_at->format('M j, Y g:i a') }}</p>
        </div>
        <x-order-status-badge :status="$order->status" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        {{-- Shipping --}}
        <div class="card p-6 animate-fade-in-up">
            <h2 class="flex items-center gap-2 text-lg font-bold text-stone-900">
                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                Shipping address
            </h2>
            <address class="mt-4 text-sm not-italic leading-relaxed text-stone-600">
                <span class="font-semibold text-stone-800">{{ $order->shipping_name }}</span><br>
                {{ $order->shipping_address }}<br>
                {{ $order->shipping_city }}, {{ $order->shipping_zip }}<br>
                {{ $order->shipping_country }}
            </address>
        </div>

        {{-- Summary --}}
        <div class="card p-6 animate-fade-in-up [animation-delay:0.1s]">
            <h2 class="flex items-center gap-2 text-lg font-bold text-stone-900">
                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185zM9.75 9.75a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm8.25 4.5h-.75a.75.75 0 000 1.5h.75a.75.75 0 000-1.5zm-4.5 0a2.25 2.25 0 100 4.5 2.25 2.25 0 000-4.5zm0-11.25a.75.75 0 01.75.75v7.5a.75.75 0 01-1.5 0v-7.5a.75.75 0 01.75-.75z"/></svg>
                Summary
            </h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between text-stone-600">
                    <dt>Items</dt>
                    <dd>{{ $order->items->count() }}</dd>
                </div>
                <div class="flex justify-between border-t border-dashed border-stone-200 pt-2 text-stone-600">
                    <dt>Status</dt>
                    <dd>{{ $order->status->label() }}</dd>
                </div>
                <div class="flex justify-between border-t border-dashed border-stone-200 pt-2 text-stone-900">
                    <dt class="font-semibold">Total</dt>
                    <dd class="text-xl font-bold text-emerald-700">${{ $order->total }}</dd>
                </div>
            </dl>
        </div>

        {{-- Items (span 2) --}}
        <div class="table-wrap animate-fade-in-up [animation-delay:0.2s] lg:col-span-3">
            <div class="border-b border-stone-100 px-5 py-4">
                <h2 class="text-lg font-bold text-stone-900">Items</h2>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Options</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th class="text-right">Line total</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td class="font-medium text-stone-800">{{ $item->product_name }}</td>
                        <td class="text-stone-500">{{ $item->variant_label ?: '-' }}</td>
                        <td>${{ $item->unit_price }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-right font-bold text-emerald-700">${{ $item->line_total }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection