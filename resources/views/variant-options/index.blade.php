@extends("base")

@section("content")
    <h1>Options for {{ $variant->name }}</h1>
    <p>
        <a href="{{ route('products.variants.show', [$variant->product_id, $variant]) }}">Back to variant</a> |
        <a href="{{ route('variants.variant-options.create', $variant) }}">Add Option</a>
    </p>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($variantOptions as $variantOption)
            <tr>
                <td>{{ $variantOption->name }}</td>
                <td>
                    <a href="{{ route('variants.variant-options.show', [$variant, $variantOption]) }}">View</a>
                    <a href="{{ route('variants.variant-options.edit', [$variant, $variantOption]) }}">Edit</a>
                    <form action="{{ route('variants.variant-options.destroy', [$variant, $variantOption]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this option?');">
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