@extends("base")

@section("content")
    <h1>{{ $product->name }}</h1>
    <p>
        <a href="{{ route('products.index') }}">Back to products</a> |
        <a href="{{ route('products.edit', $product) }}">Edit</a>
    </p>

    <h2>Variants</h2>
    <p><a href="{{ route('products.variants.create', $product) }}">Add Variant</a></p>
    @if($product->variants->isEmpty())
        <p>No variants.</p>
    @else
        <ul>
            @foreach($product->variants as $variant)
                <li>
                    <a href="{{ route('products.variants.show', [$product, $variant]) }}">{{ $variant->name }}</a>
                    ({{ $variant->variantOptions->count() }} options)
                </li>
            @endforeach
        </ul>
    @endif

    <h2>Product Variants (price / stock)</h2>
    <p>
        <a href="{{ route('products.product-variants.create', $product) }}">Add Product Variant</a> |
        <a href="{{ route('products.product-variants.assign', $product) }}">Generate by Combination</a>
    </p>
    @if($product->productVariants->isEmpty())
        <p>No product variants.</p>
    @else
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th>Combination</th>
                <th>Price</th>
                <th>Stock</th>
            </tr>
            </thead>
            <tbody>
            @foreach($product->productVariants as $productVariant)
                <tr>
                    <td>{{ $productVariant->combinationLabel() }}</td>
                    <td>{{ $productVariant->price }}</td>
                    <td>{{ $productVariant->stock }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection