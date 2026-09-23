@extends('base')

@section('content')
    <h1>Checkout</h1>

    @if ($cart->items->isEmpty())
        <p>Your cart is empty. <a href="{{ route('shop.index') }}">Browse the shop</a>.</p>
    @else
        <h2>Order Summary</h2>
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
            @foreach($cart->items as $item)
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
        <p><strong>Total: ${{ $cart->total() }}</strong></p>

        <h2>Shipping Details</h2>
        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <p>
                <label for="shipping_name">Name</label>
                <input id="shipping_name" type="text" name="shipping_name" value="{{ old('shipping_name') }}" required>
                @error('shipping_name')
                    <div>{{ $message }}</div>
                @enderror
            </p>
            <p>
                <label for="shipping_address">Address</label>
                <input id="shipping_address" type="text" name="shipping_address" value="{{ old('shipping_address') }}" required>
                @error('shipping_address')
                    <div>{{ $message }}</div>
                @enderror
            </p>
            <p>
                <label for="shipping_city">City</label>
                <input id="shipping_city" type="text" name="shipping_city" value="{{ old('shipping_city') }}" required>
                @error('shipping_city')
                    <div>{{ $message }}</div>
                @enderror
            </p>
            <p>
                <label for="shipping_zip">Zip / Postal Code</label>
                <input id="shipping_zip" type="text" name="shipping_zip" value="{{ old('shipping_zip') }}" required>
                @error('shipping_zip')
                    <div>{{ $message }}</div>
                @enderror
            </p>
            <p>
                <label for="shipping_country">Country</label>
                <input id="shipping_country" type="text" name="shipping_country" value="{{ old('shipping_country') }}" required>
                @error('shipping_country')
                    <div>{{ $message }}</div>
                @enderror
            </p>
            <p>
                <button type="submit">Place Order</button>
                <a href="{{ route('cart.index') }}">Back to cart</a>
            </p>
        </form>
    @endif
@endsection