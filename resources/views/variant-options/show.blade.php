@extends("base")

@section("content")
    <h1>{{ $variantOption->name }}</h1>
    <p>Variant: <a href="{{ route('products.variants.show', [$variantOption->variant->product_id, $variant]) }}">{{ $variant->name }}</a></p>
    <p>
        <a href="{{ route('variants.variant-options.index', $variant) }}">Back to options</a> |
        <a href="{{ route('variants.variant-options.edit', [$variant, $variantOption]) }}">Edit</a>
    </p>
@endsection