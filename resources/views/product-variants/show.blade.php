@extends("base")

@section("content")
    <h1>{{ $productVariant->combinationLabel() }}</h1>
    <p>Product: <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></p>
    <p>Price: {{ $productVariant->price }}</p>
    <p>Stock: {{ $productVariant->stock }}</p>
    <p>
        <a href="{{ route('products.product-variants.index', $product) }}">Back to product variants</a> |
        <a href="{{ route('products.product-variants.edit', [$product, $productVariant]) }}">Edit</a>
    </p>
@endsection