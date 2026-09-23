@extends('base')

@section('content')
    <h1>Create Brand</h1>
    <form action="{{ route('brands.store') }}" method="POST">
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
            <a href="{{ route('brands.index') }}">Cancel</a>
        </p>
    </form>
@endsection