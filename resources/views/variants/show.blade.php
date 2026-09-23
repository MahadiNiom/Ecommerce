@extends("base")

@section("content")
    <h1>{{ $variant->name }} ({{ $product->name }})</h1>
    <p>
        <a href="{{ route('products.variants.index', $product) }}">Back to variants</a> |
        <a href="{{ route('products.variants.edit', [$product, $variant]) }}">Edit</a>
    </p>

    <h2>Variant Options</h2>
    <p><a href="{{ route('variants.variant-options.create', $variant) }}">Add Option</a></p>
    @if($variant->variantOptions->isEmpty())
        <p>No options.</p>
    @else
        <ul>
            @foreach($variant->variantOptions as $option)
                <li>
                    <a href="{{ route('variants.variant-options.show', [$variant, $option]) }}">{{ $option->name }}</a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection