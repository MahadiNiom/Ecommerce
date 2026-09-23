@extends("base")

@section("content")
    <h1>Create Product Variant for {{ $product->name }}</h1>
    <form action="{{ route('products.product-variants.store', $product) }}" method="POST">
        @csrf
        @foreach($variants as $variant)
            <div>
                <label for="option_{{ $variant->id }}">{{ $variant->name }}</label>
                <select id="option_{{ $variant->id }}" name="options[{{ $variant->id }}]">
                    <option value="">Select an option</option>
                    @foreach($variant->variantOptions as $option)
                        <option value="{{ $option->id }}" @selected(old("options.{$variant->id}") == $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
                @error('options')
                    <div>{{ $message }}</div>
                @enderror
            </div>
        @endforeach
        <div>
            <label for="price">Price</label>
            <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price') }}">
            @error('price')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="stock">Stock</label>
            <input type="number" id="stock" name="stock" min="0" value="{{ old('stock') }}">
            @error('stock')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <button type="submit">Create</button>
    </form>
    <p><a href="{{ route('products.product-variants.index', $product) }}">Back to product variants</a></p>
@endsection