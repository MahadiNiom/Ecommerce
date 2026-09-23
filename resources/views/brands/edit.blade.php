@extends('base')

@section('content')
    <h1>Edit Brand: {{ $brand->name }}</h1>
    <form action="{{ route('brands.update', $brand) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $brand->name) }}" required>
            @error('name')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <p>
            <button type="submit">Save</button>
            <a href="{{ route('brands.index') }}">Cancel</a>
        </p>
    </form>
@endsection