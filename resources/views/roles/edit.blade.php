@extends('base')

@section('content')
    <h1>Edit Role: {{ $role->name }}</h1>
    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')
        <p>
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name', $role->name) }}" required>
        </p>
        <fieldset>
            <legend>Permissions</legend>
            @forelse($permissions as $permission)
                <p>
                    <label>
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                               {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->all())) ? 'checked' : '' }}>
                        {{ $permission->name }}
                    </label>
                </p>
            @empty
                <p>No permissions exist yet. <a href="{{ route('permissions.create') }}">Create one</a>.</p>
            @endforelse
        </fieldset>
        <p>
            <button type="submit">Save</button>
            <a href="{{ route('roles.index') }}">Cancel</a>
        </p>
    </form>
@endsection