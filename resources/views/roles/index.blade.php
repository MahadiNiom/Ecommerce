@extends('base')

@section('title', 'Roles - ' . config('app.name'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="page-title">Roles</h1>
            <p class="page-subtitle">Control what your team can do in the admin.</p>
        </div>
        @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::CREATE_ROLES]))
        <a href="{{ route('roles.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Create role
        </a>
        @endif
    </div>

    <div class="table-wrap mt-8 animate-fade-in-up [animation-delay:0.1s]">
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Permissions</th>
                <th>Users</th>
                <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td class="font-medium text-stone-900">{{ $role->name }}</td>
                    <td><span class="badge-stone">{{ $role->permissions->count() }}</span></td>
                    <td><span class="badge-green">{{ $role->users_count }}</span></td>
                    <td>
                        <div class="flex items-center justify-end gap-2">
                            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::EDIT_ROLES]))
                                <a href="{{ route('roles.edit', $role) }}" class="btn-outline btn-sm">Edit</a>
                            @endif
                            @if (in_array($role->name, ['admin', 'user'], true))
                                <span class="badge-amber">Protected</span>
                            @elseif (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::DELETE_ROLES]))
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection