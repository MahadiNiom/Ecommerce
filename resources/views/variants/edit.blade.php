@extends("base")

@section("content")
    <h1>Edit Variant</h1>
    <form action="{{ route('products.variants.update', [$product, $variant]) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $variant->name) }}">
            @error('name')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <button type="submit">Update</button>
    </form>
    <p><a href="{{ route('products.variants.show', [$product, $variant]) }}">Back to variant</a></p>
@endsection