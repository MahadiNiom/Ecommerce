@extends("base")

@section("content")
    <h1>Variants for {{ $product->name }}</h1>
    <p>
        <a href="{{ route('products.show', $product) }}">Back to product</a> |
        <a href="{{ route('products.variants.create', $product) }}">Add Variant</a>
    </p>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Name</th>
            <th>Options</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($variants as $variant)
            <tr>
                <td>{{ $variant->name }}</td>
                <td>{{ $variant->variantOptions->count() }}</td>
                <td>
                    <a href="{{ route('products.variants.show', [$product, $variant]) }}">View</a>
                    <a href="{{ route('products.variants.edit', [$product, $variant]) }}">Edit</a>
                    <form action="{{ route('products.variants.destroy', [$product, $variant]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this variant?');">
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