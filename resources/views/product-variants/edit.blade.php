@extends("base")

@section("content")
    <h1>Edit Product Variant</h1>
    <form action="{{ route('products.product-variants.update', [$product, $productVariant]) }}" method="POST">
        @csrf
        @method('PUT')
        @php
            $selectedByVariant = $productVariant->variantOptions->pluck('id', 'variant_id');
        @endphp
        @foreach($variants as $variant)
            <div>
                <label for="option_{{ $variant->id }}">{{ $variant->name }}</label>
                <select id="option_{{ $variant->id }}" name="options[{{ $variant->id }}]">
                    <option value="">Select an option</option>
                    @foreach($variant->variantOptions as $option)
                        <option value="{{ $option->id }}" @selected(old("options.{$variant->id}", $selectedByVariant->get($variant->id)) == $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
                @error('options')
                    <div>{{ $message }}</div>
                @enderror
            </div>
        @endforeach
        <div>
            <label for="price">Price</label>
            <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $productVariant->price) }}">
            @error('price')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="stock">Stock</label>
            <input type="number" id="stock" name="stock" min="0" value="{{ old('stock', $productVariant->stock) }}">
            @error('stock')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <button type="submit">Update</button>
    </form>
    <p><a href="{{ route('products.product-variants.show', [$product, $productVariant]) }}">Back to product variant</a></p>
@endsection