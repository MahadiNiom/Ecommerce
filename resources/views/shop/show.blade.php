@extends('base')

@section('content')
    <p><a href="{{ route('shop.index') }}">Back to shop</a></p>
    <h1>{{ $product->name }}</h1>

    @if ($product->category)
        <p>Category: <a href="{{ route('shop.index', ['category' => $product->category_id]) }}">{{ $product->category->name }}</a></p>
    @endif
    @if ($product->brand)
        <p>Brand: <a href="{{ route('shop.index', ['brand' => $product->brand_id]) }}">{{ $product->brand->name }}</a></p>
    @endif
    @if ($product->description)
        <p>{{ $product->description }}</p>
    @endif
    @if ($product->tags->isNotEmpty())
        <p>
            Tags:
            @foreach($product->tags as $tag)
                <a href="{{ route('shop.index', ['tag' => $tag->id]) }}">{{ $tag->name }}</a>
                @unless($loop->last)
                    ,
                @endunless
            @endforeach
        </p>
    @endif

    <h2>Variants</h2>
    @if ($product->productVariants->isEmpty())
        @if ($product->price !== null)
            <p>Price: ${{ $product->price }}</p>
            @if ($product->stock > 0)
                @auth
                    <form action="{{ route('cart.store') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit">Add to Cart</button>
                    </form>
                    <form action="{{ route('wishlist.store') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit">Add to Wishlist</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Login to buy</a>
                @endauth
            @else
                <em>Out of stock</em>
            @endif
        @else
            <p>This product is not available for purchase yet.</p>
        @endif
    @else
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th>Options</th>
                <th>Price</th>
                <th>Stock</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($product->productVariants as $variant)
                <tr>
                    <td>{{ $variant->combinationLabel() }}</td>
                    <td>${{ $variant->price }}</td>
                    <td>{{ $variant->stock }}</td>
                    <td>
                        @if ($variant->stock > 0)
                            @auth
                                <form action="{{ route('cart.store') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit">Add to Cart</button>
                                </form>
                                <form action="{{ route('wishlist.store') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                                    <button type="submit">Add to Wishlist</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}">Login to buy</a>
                            @endauth
                        @else
                            <em>Out of stock</em>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection