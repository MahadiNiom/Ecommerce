@extends("base")

@section("content")
    <h1>Create Variant for {{ $product->name }}</h1>
    <form action="{{ route('products.variants.store', $product) }}" method="POST">
        @csrf
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}">
            @error('name')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <button type="submit">Create</button>
    </form>
    <p><a href="{{ route('products.variants.index', $product) }}">Back to variants</a></p>
@endsection