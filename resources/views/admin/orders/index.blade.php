@extends('base')

@section('content')
    <h1>Order Management</h1>

    <form action="{{ route('admin.orders.index') }}" method="GET">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="">All</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        <button type="submit">Filter</button>
        <a href="{{ route('admin.orders.index') }}">Clear</a>
    </form>

    @if ($orders->isEmpty())
        <p>No orders found.</p>
    @else
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Status</th>
                <th>Items</th>
                <th>Total</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user?->name }}<br><small>{{ $order->user?->email }}</small></td>
                    <td>{{ $order->created_at->format('M j, Y g:i a') }}</td>
                    <td>{{ $order->status->label() }}</td>
                    <td>{{ $order->items_count }}</td>
                    <td>${{ $order->total }}</td>
                    <td><a href="{{ route('admin.orders.show', $order) }}">View</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $orders->links() }}
    @endif
@endsection