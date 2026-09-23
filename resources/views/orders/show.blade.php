@extends('base')

@section('content')
    <p><a href="{{ route('orders.index') }}">Back to orders</a></p>
    <h1>Order #{{ $order->id }}</h1>
    <p>Placed on {{ $order->created_at->format('M j, Y g:i a') }}</p>
    <p>Status: {{ $order->status->label() }}</p>
    <p>Total: ${{ $order->total }}</p>

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
@endsection