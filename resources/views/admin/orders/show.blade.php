@extends('base')

@section('title', 'Order #' . $order->id . ' - ' . config('app.name'))

@section('content')
    <a href="{{ route('admin.orders.index') }}" class="link inline-flex items-center gap-1 text-sm animate-fade-in-up">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back to orders
    </a>

    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="page-title">Order #{{ $order->id }}</h1>
            <p class="page-subtitle">
                Placed on {{ $order->created_at->format('M j, Y g:i a') }} by
                <a href="mailto:{{ $order->user?->email }}" class="link">{{ $order->user?->name ?? 'Guest' }}</a>
            </p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        {{-- Items --}}
        <div class="table-wrap animate-fade-in-up lg:col-span-2">
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
                <tfoot>
                <tr class="border-t-2 border-stone-100 bg-stone-50 hover:bg-stone-50">
                    <td colspan="4" class="text-right font-semibold text-stone-900">Total</td>
                    <td class="text-right text-xl font-bold text-emerald-700">${{ $order->total }}</td>
                </tr>
                </tfoot>
            </table>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status manager --}}
            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::UPDATE_ORDER_STATUS]))
                <div class="card p-6 animate-fade-in-up [animation-delay:0.1s]">
                <h2 class="flex items-center gap-2 text-lg font-bold text-stone-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225L9.6 17.525l2.64-4.173l2.51 2.225l-2.51 2.225zM5.5 15l1.5-1.5M10 10l1.5-1.5M5 19.5h.008H5zM7.5 7.5l3-1.5m-1 8.25l6-6M12.75 4.5L15.75 1.5m.75 9h1.5m3.75 0h.008H21z"/></svg>
                    </span>
                    Update status
                </h2>
                <div class="mt-4">
                    <x-order-status-badge :status="$order->status" />
                </div>
                <form action="{{ route('admin.orders.status.update', $order) }}" method="POST" class="mt-5">
                    @csrf
                    @method('PATCH')
                    <label for="status" class="label">Change status</label>
                    <select id="status" name="status" class="select">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" {{ $order->status === $status ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                    <button type="submit" class="btn-primary mt-4 w-full">Update status</button>
                </form>
            </div>
            @endif

            {{-- Shipping --}}
            <div class="card p-6 animate-fade-in-up [animation-delay:0.2s]">
                <h2 class="flex items-center gap-2 text-lg font-bold text-stone-900">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    Shipping
                </h2>
                <address class="mt-4 text-sm not-italic leading-relaxed text-stone-600">
                    <span class="font-semibold text-stone-800">{{ $order->shipping_name }}</span><br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_zip }}<br>
                    {{ $order->shipping_country }}
                </address>
            </div>
        </div>
    </div>
@endsection