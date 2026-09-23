@extends('base')

@section('content')
    <h1>Edit Permission: {{ $permission->name }}</h1>
    <form action="{{ route('permissions.update', $permission) }}" method="POST">
        @csrf
        @method('PUT')
        <p>
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name', $permission->name) }}" required>
        </p>
        <p>
            <button type="submit">Save</button>
            <a href="{{ route('permissions.index') }}">Cancel</a>
        </p>
    </form>
@endsection