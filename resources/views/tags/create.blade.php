@extends('base')

@section('content')
    <h1>Create Tag</h1>
    <form action="{{ route('tags.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div>{{ $message }}</div>
            @enderror
        </div>
        <p>
            <button type="submit">Create</button>
            <a href="{{ route('tags.index') }}">Cancel</a>
        </p>
    </form>
@endsection