@extends('base')

@section('content')
    <p><a href="{{ route('admin.orders.index') }}">Back to orders</a></p>
    <h1>Order #{{ $order->id }}</h1>
    <p>Placed on {{ $order->created_at->format('M j, Y g:i a') }}</p>
    <p>Customer: {{ $order->user?->name }} ({{ $order->user?->email }})</p>

    <h2>Status</h2>
    <form action="{{ route('admin.orders.status.update', $order) }}" method="POST">
        @csrf
        @method('PATCH')
        <label for="status">Current status: {{ $order->status->label() }}</label>
        <select id="status" name="status">
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" {{ $order->status === $status ? 'selected' : '' }}>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>
        @error('status')
            <div>{{ $message }}</div>
        @enderror
        <button type="submit">Update Status</button>
    </form>

    <h2>Shipping</h2>
    <p>
        {{ $order->shipping_name }}<br>
        {{ $order->shipping_address }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_zip }}<br>
        {{ $order->shipping_country }}
    </p>

    <h2>Items</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Product</th>
            <th>Options</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Line Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->variant_label ?: '-' }}</td>
                <td>${{ $item->unit_price }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ $item->line_total }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p><strong>Total: ${{ $order->total }}</strong></p>
@endsection