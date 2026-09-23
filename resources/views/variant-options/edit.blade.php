@extends("base")

@section("content")
    <h1>Edit Option</h1>
    <form action="{{ route('variants.variant-options.update', [$variant, $variantOption]) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $variantOption->name) }}">
            @error('name')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <button type="submit">Update</button>
    </form>
    <p><a href="{{ route('variants.variant-options.show', [$variant, $variantOption]) }}">Back to option</a></p>
@endsection