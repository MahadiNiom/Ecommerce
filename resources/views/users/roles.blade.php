@extends('base')

@section('title', 'Assign Roles - ' . config('app.name'))

@section('content')
    <a href="{{ route('users.index') }}" class="link inline-flex items-center gap-1 text-sm animate-fade-in-up">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back to users
    </a>

    <div class="mt-6 max-w-xl animate-fade-in-up">
        <div class="flex items-center gap-4">
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-700 text-xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            <div>
                <h1 class="page-title">Assign roles for {{ $user->name }}</h1>
                <p class="page-subtitle">{{ $user->email }}</p>
            </div>
        </div>

        <div class="card mt-6 p-6 sm:p-8">
            <form action="{{ route('users.roles.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-2">
                    @foreach ($roles as $role)
                        <label class="flex items-center justify-between rounded-xl border border-stone-200 px-4 py-3 text-sm text-stone-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50/50">
                            <span class="font-medium">{{ $role->name }}</span>
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="checkbox" {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                        </label>
                    @endforeach
                </div>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('users.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Save roles</button>
                </div>
            </form>
        </div>
    </div>
@endsection