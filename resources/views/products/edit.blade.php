@extends("base")

@section("content")
    <h1>Edit Product</h1>
    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}">
            @error('name')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="price">Price</label>
            <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}">
            @error('price')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="stock">Stock</label>
            <input type="number" id="stock" name="stock" min="0" value="{{ old('stock', $product->stock) }}">
            @error('stock')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <p><small>Price and stock apply only to products without variants.</small></p>
        <div>
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <option value="">-- None --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ str_repeat('&nbsp;&nbsp;', $category->depth) }}{{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="brand_id">Brand</label>
            <select id="brand_id" name="brand_id">
                <option value="">-- None --</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
            @error('brand_id')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <fieldset>
            <legend>Tags</legend>
            @forelse($tags as $tag)
                <label style="display:block;">
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                           {{ in_array($tag->id, old('tags', $product->tags->pluck('id')->all())) ? 'checked' : '' }}>
                    {{ $tag->name }}
                </label>
            @empty
                <p>No tags exist yet. <a href="{{ route('tags.create') }}">Create one</a>.</p>
            @endforelse
            @error('tags')
                <div>{{ $message }}</div>
            @enderror
        </fieldset>
        <button type="submit">Update</button>
    </form>
    <p><a href="{{ route('products.show', $product) }}">Back to product</a></p>
@endsection