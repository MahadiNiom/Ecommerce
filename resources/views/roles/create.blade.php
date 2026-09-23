@extends('base')

@section('content')
    <h1>Create Role</h1>
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <p>
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
        </p>
        <fieldset>
            <legend>Permissions</legend>
            @forelse($permissions as $permission)
                <p>
                    <label>
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                        {{ $permission->name }}
                    </label>
                </p>
            @empty
                <p>No permissions exist yet. <a href="{{ route('permissions.create') }}">Create one</a>.</p>
            @endforelse
        </fieldset>
        <p>
            <button type="submit">Create</button>
            <a href="{{ route('roles.index') }}">Cancel</a>
        </p>
    </form>
@endsection