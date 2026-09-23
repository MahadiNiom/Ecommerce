@extends('base')

@section('content')
    <h1>Create Permission</h1>
    <form action="{{ route('permissions.store') }}" method="POST">
        @csrf
        <p>
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
        </p>
        <p>
            <button type="submit">Create</button>
            <a href="{{ route('permissions.index') }}">Cancel</a>
        </p>
    </form>
@endsection