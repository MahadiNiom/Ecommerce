@extends('base')

@section('content')
    <h1>Your Wishlist</h1>

    @if ($wishlist->isEmpty())
        <p>Your wishlist is empty. <a href="{{ route('shop.index') }}">Browse the shop</a>.</p>
    @else
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th>Product</th>
                <th>Options</th>
                <th>Price</th>
                <th>Stock</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($wishlist as $wishlistItem)
                <tr>
                    <td>{{ $wishlistItem->product_name }}</td>
                    <td>{{ $wishlistItem->variant_label ?: '-' }}</td>
                    <td>${{ $wishlistItem->unit_price }}</td>
                    <td>{{ $wishlistItem->stock ?? '-' }}</td>
                    <td>
                        @if (($wishlistItem->stock ?? 0) > 0)
                            <form action="{{ route('cart.store') }}" method="POST" style="display:inline;">
                                @csrf
                                @if ($wishlistItem->product_variant_id !== null)
                                    <input type="hidden" name="product_variant_id" value="{{ $wishlistItem->product_variant_id }}">
                                @else
                                    <input type="hidden" name="product_id" value="{{ $wishlistItem->product_id }}">
                                @endif
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit">Add to Cart</button>
                            </form>
                        @endif
                        <form action="{{ route('wishlist.destroy', $wishlistItem) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection