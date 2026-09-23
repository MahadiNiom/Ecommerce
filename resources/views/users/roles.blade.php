@extends('base')

@section('content')
    <h1>Assign Roles: {{ $user->name }}</h1>
    <form action="{{ route('users.roles.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        @foreach($roles as $role)
            <p>
                <label>
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                           {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                    {{ $role->name }}
                </label>
            </p>
        @endforeach
        <p>
            <button type="submit">Save</button>
            <a href="{{ route('users.index') }}">Cancel</a>
        </p>
    </form>
@endsection