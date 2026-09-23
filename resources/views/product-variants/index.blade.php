@extends("base")

@section("content")
    <h1>Product Variants for {{ $product->name }}</h1>
    <p>
        <a href="{{ route('products.show', $product) }}">Back to product</a> |
        <a href="{{ route('products.product-variants.create', $product) }}">Add Product Variant</a> |
        <a href="{{ route('products.product-variants.assign', $product) }}">Generate by Combination</a>
    </p>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Combination</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($productVariants as $productVariant)
            <tr>
                <td>{{ $productVariant->combinationLabel() }}</td>
                <td>{{ $productVariant->price }}</td>
                <td>{{ $productVariant->stock }}</td>
                <td>
                    <a href="{{ route('products.product-variants.show', [$product, $productVariant]) }}">View</a>
                    <a href="{{ route('products.product-variants.edit', [$product, $productVariant]) }}">Edit</a>
                    <form action="{{ route('products.product-variants.destroy', [$product, $productVariant]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this product variant?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection