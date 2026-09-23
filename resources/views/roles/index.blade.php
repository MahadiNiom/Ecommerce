@extends('base')

@section('content')
    <h1>Roles</h1>
    <p><a href="{{ route('roles.create') }}">Create Role</a></p>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Name</th>
            <th>Permissions</th>
            <th>Users</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->name }}</td>
                <td>{{ $role->permissions->count() }}</td>
                <td>{{ $role->users_count }}</td>
                <td>
                    <a href="{{ route('roles.edit', $role) }}">Edit</a>
                    @if (in_array($role->name, ['admin', 'user'], true))
                        <em>Protected</em>
                    @else
                        <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this role?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection