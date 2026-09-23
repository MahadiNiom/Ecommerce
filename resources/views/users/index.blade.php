@extends('base')

@section('content')
    <h1>Users</h1>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Roles</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->roles->pluck('name')->count() ? $user->roles->pluck('name')->implode(', ') : 'None' }}</td>
                <td><a href="{{ route('users.roles.edit', $user) }}">Assign roles</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection