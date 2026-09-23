@extends('base')

@section('content')
    <h1>Your Orders</h1>

    @if ($orders->isEmpty())
        <p>You have no orders yet. <a href="{{ route('shop.index') }}">Start shopping</a>.</p>
    @else
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th>Order #</th>
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
                    <td>{{ $order->created_at->format('M j, Y') }}</td>
                    <td>{{ $order->status->label() }}</td>
                    <td>{{ $order->items_count }}</td>
                    <td>${{ $order->total }}</td>
                    <td><a href="{{ route('orders.show', $order) }}">View</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection