@extends('base')

@section('content')
    <h1>Your Cart</h1>

    @if ($cart->items->isEmpty())
        <p>Your cart is empty. <a href="{{ route('shop.index') }}">Browse the shop</a>.</p>
    @else
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th>Product</th>
                <th>Options</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Line Total</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($cart->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->variant_label ?: '-' }}</td>
                    <td>${{ $item->unit_price }}</td>
                    <td>
                        <form action="{{ route('cart.update', $item) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99">
                            <button type="submit">Update</button>
                        </form>
                    </td>
                    <td>${{ $item->line_total }}</td>
                    <td>
                        <form action="{{ route('cart.destroy', $item) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <p><strong>Total: ${{ $cart->total() }}</strong></p>
        <p><a href="{{ route('checkout.create') }}">Proceed to Checkout</a></p>
    @endif
@endsection