@extends('base')

@section('content')
    <h1>Permissions</h1>
    <p><a href="{{ route('permissions.create') }}">Create Permission</a></p>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Name</th>
            <th>Roles</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($permissions as $permission)
            <tr>
                <td>{{ $permission->name }}</td>
                <td>{{ $permission->roles_count }}</td>
                <td>
                    <a href="{{ route('permissions.edit', $permission) }}">Edit</a>
                    <form action="{{ route('permissions.destroy', $permission) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this permission?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection