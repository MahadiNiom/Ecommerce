@extends('base')

@section('title', 'Permissions - ' . config('app.name'))

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <div>
            <h1 class="page-title">Permissions</h1>
            <p class="page-subtitle">Define the fine-grained actions available to roles.</p>
        </div>
        @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::CREATE_PERMISSIONS]))
        <a href="{{ route('permissions.create') }}" class="btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Create permission
        </a>
        @endif
    </div>

    <div class="table-wrap mt-8 animate-fade-in-up [animation-delay:0.1s]">
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Roles</th>
                <th class="text-right">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($permissions as $permission)
                <tr>
                    <td class="font-medium text-stone-900">{{ $permission->name }}</td>
                    <td><span class="badge-stone">{{ $permission->roles_count }}</span></td>
                    <td>
                        <div class="flex items-center justify-end gap-2">
                            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::EDIT_PERMISSIONS]))
                                <a href="{{ route('permissions.edit', $permission) }}" class="btn-outline btn-sm">Edit</a>
                            @endif
                            @if (\App\Support\Permissions::canAny(auth()->user(), [\App\Support\Permissions::DELETE_PERMISSIONS]))
                                <form action="{{ route('permissions.destroy', $permission) }}" method="POST" onsubmit="return confirm('Delete this permission?');">
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