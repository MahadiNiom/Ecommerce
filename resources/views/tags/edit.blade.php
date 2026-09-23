@extends('base')

@section('content')
    <h1>Edit Tag: {{ $tag->name }}</h1>
    <form action="{{ route('tags.update', $tag) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $tag->name) }}" required>
            @error('name')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <p>
            <button type="submit">Save</button>
            <a href="{{ route('tags.index') }}">Cancel</a>
        </p>
    </form>
@endsection