@extends("base")

@section("content")
    <h1>Create Option for {{ $variant->name }}</h1>
    <form action="{{ route('variants.variant-options.store', $variant) }}" method="POST">
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
    <p><a href="{{ route('variants.variant-options.index', $variant) }}">Back to options</a></p>
@endsection