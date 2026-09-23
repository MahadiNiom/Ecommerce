@extends('base')

@section('title', 'Users - ' . config('app.name'))

@section('content')
    <div class="animate-fade-in-up">
        <h1 class="page-title">Users</h1>
        <p class="page-subtitle">Manage the people who have access to your store.</p>
    </div>

    <div class="table-wrap mt-8 animate-fade-in-up [animation-delay:0.1s]">
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-700 text-sm font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            <span class="font-medium text-stone-900">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="text-stone-500">{{ $user->email }}</td>
                    <td>
                        @php
                            $roleNames = $user->roles->pluck('name');
                        @endphp
                        @if ($roleNames->count())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($roleNames as $roleName)
                                    <span class="badge-green">{{ $roleName }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="badge-stone">None</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::ASSIGN_ROLES]))
                            <a href="{{ route('users.roles.edit', $user) }}" class="btn-outline btn-sm">Assign roles</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection