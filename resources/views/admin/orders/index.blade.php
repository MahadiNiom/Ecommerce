@extends('base')

@section('title', 'Order Management - ' . config('app.name'))

@section('content')
    <div class="animate-fade-in-up">
        <h1 class="page-title">Order management</h1>
        <p class="page-subtitle">Review, filter, and manage every order in your store.</p>
    </div>

    <form action="{{ route('admin.orders.index') }}" method="GET" class="card mt-6 animate-fade-in-up p-4 [animation-delay:0.1s] sm:p-5">
        <div class="grid gap-4 sm:grid-cols-[1fr_auto_auto] sm:items-end">
            <div>
                <label for="status" class="label">Filter by status</label>
                <select id="status" name="status" class="select">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Filter</button>
            @if (request('status'))
                <a href="{{ route('admin.orders.index') }}" class="btn-ghost items-end">Clear</a>
            @endif
        </div>
    </form>

    @if ($orders->isEmpty())
        <div class="card mt-8 flex flex-col items-center gap-4 px-6 py-16 text-center animate-fade-in-up">
            <span class="text-5xl" aria-hidden="true">&#128203;</span>
            <h2 class="text-lg font-bold text-stone-900">No orders found</h2>
            <p class="max-w-sm text-sm text-stone-500">Try a different status filter, or check back later.</p>
        </div>
    @else
        <div class="table-wrap mt-8 animate-fade-in-up [animation-delay:0.15s]">
            <table class="table">
                <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
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
                        <td>
                            <span class="font-medium text-stone-800">{{ $order->user?->name ?? 'Guest' }}</span>
                            <p class="text-xs text-stone-500">{{ $order->user?->email }}</p>
                        </td>
                        <td class="text-stone-500">{{ $order->created_at->format('M j, Y g:i a') }}</td>
                        <td><x-order-status-badge :status="$order->status" /></td>
                        <td><span class="badge-stone">{{ $order->items_count }}</span></td>
                        <td class="font-bold text-emerald-700">${{ $order->total }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn-outline btn-sm">View</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8 animate-fade-in-up">
            {{ $orders->links() }}
        </div>
    @endif
@endsection